<?php

namespace JingdongCloudTradeBundle\Procedure;

use Doctrine\ORM\EntityManagerInterface;
use JingdongCloudTradeBundle\Entity\Account;
use JingdongCloudTradeBundle\Entity\Category;
use JingdongCloudTradeBundle\Repository\AccountRepository;
use JingdongCloudTradeBundle\Repository\CategoryRepository;
use JingdongCloudTradeBundle\Service\Client;
use JsonRpcServerBundle\Attribute\MethodDoc;
use JsonRpcServerBundle\Attribute\MethodExpose;
use JsonRpcServerBundle\Attribute\MethodParam;
use JsonRpcServerBundle\Attribute\MethodTag;
use JsonRpcServerBundle\Exception\JsonRpcException;
use JsonRpcServerBundle\Model\JsonRpcRequest;
use JsonRpcServerBundle\Procedure\CacheableProcedure;
use Psr\Cache\CacheItemPoolInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

/**
 * 获取京东商品分类接口
 * 
 * 参考：https://developer.jdcloud.com/article/4117
 */
class CategoryListProcedure extends CacheableProcedure
{
    /**
     * @var Client
     */
    private Client $client;

    /**
     * @var EntityManagerInterface
     */
    private EntityManagerInterface $entityManager;

    /**
     * @var CategoryRepository
     */
    private CategoryRepository $categoryRepository;

    /**
     * @var AccountRepository
     */
    private AccountRepository $accountRepository;

    /**
     * @param string|null $parentId 父分类ID，不传则获取根分类
     * @param int|null $level 分类层级，1-一级分类，2-二级分类，3-三级分类
     * @param string|null $accountId 账户ID，如果不传则获取所有账户的分类
     * @param bool $forceRefresh 是否强制刷新缓存
     * @return array
     */
    #[MethodParam(description: "父分类ID，不传则获取根分类", optional: true)]
    private ?string $parentId = null;

    #[MethodParam(description: "分类层级，1-一级分类，2-二级分类，3-三级分类", optional: true)]
    private ?int $level = null;

    #[MethodParam(description: "账户ID，如果不传则获取所有账户的分类", optional: true)]
    private ?string $accountId = null;

    #[MethodParam(description: "是否强制刷新缓存", optional: true)]
    private bool $forceRefresh = false;

    public function __construct(
        Client $client,
        EntityManagerInterface $entityManager,
        CategoryRepository $categoryRepository,
        AccountRepository $accountRepository,
        CacheItemPoolInterface $cache,
        ValidatorInterface $validator
    ) {
        parent::__construct($cache, $validator);
        $this->client = $client;
        $this->entityManager = $entityManager;
        $this->categoryRepository = $categoryRepository;
        $this->accountRepository = $accountRepository;
    }

    /**
     * 获取商品分类列表
     */
    #[MethodTag('category', 'list')]
    #[MethodDoc('获取京东商品分类列表')]
    #[MethodExpose('jd.category.getList')]
    public function execute(): array
    {
        // 如果不强制刷新，先从本地查找
        if (!$this->forceRefresh) {
            $localData = $this->findFromLocal();
            if (!empty($localData)) {
                return $localData;
            }
        }

        // 获取所有京东账户
        $accounts = [];
        if ($this->accountId) {
            $account = $this->accountRepository->find($this->accountId);
            if ($account) {
                $accounts[] = $account;
            }
        } else {
            $accounts = $this->accountRepository->findBy(['enabled' => true]);
        }

        if (empty($accounts)) {
            throw new JsonRpcException("没有可用的京东账户");
        }

        $allCategories = [];
        foreach ($accounts as $account) {
            // 调用API获取分类列表
            try {
                $params = $this->buildParams($account);
                $response = $this->client->execute($account, 'jingdong.ctp.ware.category.getCatList', $params);
                
                if (isset($response['error_response'])) {
                    continue;
                }
                
                if (isset($response['jingdong_ctp_ware_category_getCatList_response']['result'])) {
                    $result = $response['jingdong_ctp_ware_category_getCatList_response']['result'];
                    if (isset($result['data']) && is_array($result['data'])) {
                        foreach ($result['data'] as $item) {
                            // 检查分类是否已存在
                            $existingCategory = $this->categoryRepository->findOneBy([
                                'account' => $account,
                                'categoryId' => (string)$item['id']
                            ]);
                            
                            if (!$existingCategory) {
                                $category = new Category();
                                $category->setAccount($account);
                                $this->fillCategoryData($category, $item);
                                $this->entityManager->persist($category);
                            } else {
                                $this->fillCategoryData($existingCategory, $item);
                            }
                            
                            $this->entityManager->flush();
                        }
                    }
                }
            } catch (\Exception $e) {
                // 记录错误但继续处理其他账号
                error_log("获取京东分类失败: " . $e->getMessage());
            }
        }

        // 从数据库中获取最新数据
        return $this->findFromLocal();
    }
    
    /**
     * 查找本地分类
     * @return array
     */
    private function findFromLocal(): array
    {
        $criteria = [];
        
        if ($this->parentId !== null) {
            $criteria['parentId'] = $this->parentId;
        } elseif ($this->level !== null) {
            $criteria['level'] = $this->level;
        } else {
            // 默认获取一级分类
            $criteria['level'] = 1;
        }
        
        if ($this->accountId !== null) {
            $account = $this->accountRepository->find($this->accountId);
            if ($account) {
                $criteria['account'] = $account;
            }
        }
        
        $categories = $this->categoryRepository->findBy($criteria, ['sort' => 'ASC', 'id' => 'ASC']);
        
        // 转换为数组格式
        $result = [];
        foreach ($categories as $category) {
            $result[] = $category->retrieveAdminArray();
        }
        
        return $result;
    }
    
    /**
     * 获取请求参数
     * @param Account $account
     * @return array
     */
    private function buildParams(Account $account): array
    {
        $params = [];
        
        if ($this->parentId !== null) {
            $params['parentId'] = $this->parentId;
        }
        
        // level可选值: 0-全部层级, 1-一级类目, 2-二级类目, 3-三级类目
        if ($this->level !== null && in_array($this->level, [0, 1, 2, 3])) {
            $params['grade'] = $this->level;
        }
        
        return $params;
    }

    /**
     * 填充分类数据
     * @param Category $category 分类对象
     * @param array $data API返回的分类数据
     * @return Category
     */
    private function fillCategoryData(Category $category, array $data): Category
    {
        if (isset($data['id'])) {
            $category->setCategoryId((string)$data['id']);
        }
        
        if (isset($data['name'])) {
            $category->setCategoryName($data['name']);
        }
        
        if (isset($data['parentId'])) {
            $category->setParentId((string)$data['parentId']);
        } else {
            $category->setParentId(null);
        }
        
        if (isset($data['grade'])) {
            $category->setLevel((int)$data['grade']);
        } else {
            // 没有层级信息时，根据是否有parentId判断
            if (empty($data['parentId'])) {
                $category->setLevel(1);
            } else {
                // 默认设为二级分类
                $category->setLevel(2);
            }
        }
        
        if (isset($data['status'])) {
            $category->setState($data['status'] ? '1' : '0');
        }
        
        // 保存原始数据到extraInfo
        $category->setExtraInfo($data);
        
        return $category;
    }

    /**
     * 获取缓存键
     */
    protected function getCacheKey(JsonRpcRequest $request): string
    {
        $params = $request->getParams();
        $key = 'jd_category_list';
        
        if (isset($params['parentId'])) {
            $key .= '_parent_' . $params['parentId'];
        }
        
        if (isset($params['level'])) {
            $key .= '_level_' . $params['level'];
        }
        
        $key .= '_acc_' . ($params['accountId'] ?? 'default');
        
        return $key;
    }

    /**
     * 获取缓存时间（秒）
     */
    protected function getCacheDuration(JsonRpcRequest $request): int
    {
        return 86400; // 24小时
    }

    /**
     * 获取缓存标签
     */
    protected function getCacheTags(JsonRpcRequest $request): array
    {
        $params = $request->getParams();
        $tags = ['jd_category'];
        
        if (isset($params['parentId'])) {
            $tags[] = 'jd_category_parent_' . $params['parentId'];
        }
        
        if (isset($params['level'])) {
            $tags[] = 'jd_category_level_' . $params['level'];
        }
        
        if (isset($params['accountId'])) {
            $tags[] = 'jd_category_account_' . $params['accountId'];
        }
        
        return $tags;
    }
}
