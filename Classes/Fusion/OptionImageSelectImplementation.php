<?php

namespace Digidennis\MageMe\Fusion;

use Neos\Flow\Annotations as Flow;
use Neos\Fusion\FusionObjects\TemplateImplementation;
use Neos\Media\Domain\Model\AssetCollection;
use Neos\Media\Domain\Repository\AssetCollectionRepository;
use Neos\Media\Domain\Repository\AssetRepository;
use Neos\Media\Domain\Repository\TagRepository;

class OptionImageSelectImplementation extends TemplateImplementation
{
    /**
     * @Flow\Inject
     * @var AssetCollectionRepository
     */
    protected $assetCollectionRepository;

    /**
     * @Flow\Inject
     * @var AssetRepository
     */
    protected $assetRepository;

    /**
     * @Flow\Inject
     * @var tagRepository
     */
    protected $tagRepository;


    public function getAssetsWithTagInCollection()
    {

        $option = \Mage::getModel('catalog/product_option')->load($this->fusionValue('optionid'));
        $values =  $collection = \Mage::getResourceModel('catalog/product_option_value_collection')
                        ->addFieldToFilter('option_id', $option->getId())
                        ->setOrder('sort_order', 'asc')
                        ->getValues($option->getStoreId());
        $assetbucket = array();
        $collectionname = $this->fusionValue('collection');
        $tagname = $this->fusionValue('tag');
        $tag = $this->tagRepository->findOneByLabel($tagname);
        $collectionsquery = $this->assetCollectionRepository->findByTitle($collectionname);
        $assetcollection = null;
        $assets = array();
        foreach ($collectionsquery as $collectionquery )
        {
            if($collectionquery->getTitle() == $collectionname)
            {
                $assetcollection = $collectionquery;
            }
        }
        if( $tag && $assetcollection )
        {
            foreach($values as $value)
            {
                $query = $this->assetRepository->createQuery();
                $query->matching(
                    $query->logicalAnd(
                        $query->contains('tags', $tag),
                        $query->equals('title', $value->getDefaultTitle())
                    )
                );
                $this->assetRepository->addImageVariantFilterClause($query);
                $this->assetRepository->addAssetCollectionToQueryConstraints($query, $assetcollection);
                $asset = $query->execute();
                if( $asset->count() )
                {
                    array_push($assets, [
                        'optionTypeId' => $value->getOptionTypeId(),
                        'sortOrder' => $value->getSortOrder(),
                        'title' => $value->getDefaultTitle(),
                        'asset' => $asset[0]
                    ]);
                }
            }
        }
        return $assets;
    }
}