<?php

namespace Tests\Unit\App\Models;

use App\Models\Product;
use PHPUnit\Framework\TestCase;

class ProductTest extends TestCase
{
    protected function model()
    {
        return new Product();
    }


    public function test_table_name()
    {
        $model = $this->model();

        $this->assertEquals(
            'products',
            $model->getTable()
        );
    }

    public function test_fillable()
    {
        $model = $this->model();

        $expectedFillable = [
            'name',
            'description',
            'price'
        ];

        $this->assertEquals(
            $expectedFillable,
            $model->getFillable()
        );
    }

    public function test_dates()
    {
        $model = $this->model();

        $expectedDates = [
            'created_at',
            'updated_at'
        ];

        $this->assertEquals(
            $expectedDates,
            $model->getDates()
        );
    }

    
}
