<?php
/**
 * Created by PhpStorm.
 * User: morgan
 * Date: 1/4/2019
 * Time: 9:38 AM
 */

namespace AppBundle\Service\Category;


use AppBundle\Repository\CategoryRepository;

class Category implements CategoryInterface
{

    private $categoryRepository;

    public function __construct(CategoryRepository $categoryRepository)
    {
        $this->categoryRepository = $categoryRepository;
    }


    public function getAllCategories()
    {
        $categories = $this->categoryRepository->findAll();
        return $categories;
    }
}