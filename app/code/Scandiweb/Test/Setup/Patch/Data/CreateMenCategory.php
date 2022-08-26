<?php
//...

namespace Scandiweb\Test\Setup\Patch\Data;

use Magento\Catalog\Model\ResourceModel\Category\CollectionFactory as CategoryCollectionFactory;
use Magento\Catalog\Setup\CategorySetup;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Store\Model\StoreManagerInterface;

class CreateMenCategory implements DataPatchInterface {
    /**
     * @var StoreManagerInterface
     */
    protected StoreManagerInterface $storeManagerInterface;

    /**
     * @var CategorySetup
     */
    protected CategorySetup $categorySetup;

    /**
     * @var CategoryCollectionFactory
     */
    protected CategoryCollectionFactory $categoryCollectionFactory;

    /**
     * CreateCategory constructor
     * @param CategorySetup $categorySetup
     * @param CategoryCollectionFactory $categoryCollectionFactory
     */
    public function __construct(
        StoreManagerInterface     $storeManagerInterface,
        CategorySetup             $categorySetup,
        CategoryCollectionFactory $categoryCollectionFactory
    ) {
        $this->categorySetup = $categorySetup;
        $this->categoryCollectionFactory = $categoryCollectionFactory;
        $this->storeManagerInterface = $storeManagerInterface;
    }

    /**
     * {@inheritDoc}
     */
    public static function getDependencies(): array {
        return [];
    }

    public function apply() {
        //Get's root category ID, since we want it to be the child of root
        $parentId = $this->storeManagerInterface->getStore()->getRootCategoryId();

        $collection = $this->categoryCollectionFactory->create()->addAttributeToFilter('url_key', ['eq' => 'men']);
        $newCategory = $collection->getFirstItem();

        if ($parentId && !$newCategory->getId()) {
            $newCategory = $this->categorySetup->createCategory(
                [
                    'data' => [
                        'parent_id' => $parentId,
                        'name' => 'Men',
                        'is_active' => true,
                        'include_in_menu' => true,
                    ],
                ]
            );
            $newCategory->save();
        }
    }

    /**
     * {@inheritDoc}
     */
    public function getAliases(): array {
        return [];
    }
}
