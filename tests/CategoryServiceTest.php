<?php

namespace App\Tests;

use App\Entity\Category;
use App\Exception\ValidationException;
use App\Service\CategoryService;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class CategoryServiceTest extends TestCase
{
    private CategoryService $categoryService;
    private MockObject $validator;

    protected function setUp(): void
    {
        $this->validator = $this->createMock(ValidatorInterface::class);
        $this->categoryService = new CategoryService(
            $this->validator,
            $this->createMock(EntityManagerInterface::class)
        );
    }

    public function test_createAndFlush_validationEmptyFailed_exceptionThrown(): void
    {
        $this->validator->method('validate')->willReturn(new ConstraintViolationList([
            new ConstraintViolation('test_error', null, [], null, null, null)
        ]));

        $this->expectException(ValidationException::class);
        $this->categoryService->createAndFlush('');
    }

    public function test_createAndFlush_validationFailed_exceptionThrown(): void
    {
        $this->validator->method('validate')->willReturn(new ConstraintViolationList([
            new ConstraintViolation('test_error', null, [], null, null, null)
        ]));

        $this->expectException(ValidationException::class);
        $this->categoryService->createAndFlush('te');
    }

    public function test_createAndFlush_validationSuccess_returnCategory(): void
    {
        $this->validator->method('validate')->willReturn(new ConstraintViolationList([]));
        $category = $this->categoryService->createAndFlush('test_category');

        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals('test_category', $category->getTitle());
    }
}
