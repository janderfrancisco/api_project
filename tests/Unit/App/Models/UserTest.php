<?php

namespace Tests\Unit\App\Models;

use App\Models\User;
use PHPUnit\Framework\TestCase;

class UserTest extends TestCase
{
    protected function model()
    {
        return new User();
    }

    public function test_traits()
    {
        $model = $this->model();

        $expectedTraits = [
            \Illuminate\Notifications\Notifiable::class,
        ];

        $this->assertEquals(
            $expectedTraits,
            array_keys(class_uses($model))
        );
    }

    public function test_table_name()
    {
        $model = $this->model();

        $this->assertEquals(
            'users',
            $model->getTable()
        );
    }

    public function test_fillable()
    {
        $model = $this->model();

        $expectedFillable = [
            'name',
            'username',
            'email',
            'password',
            'active'
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

    public function test_hidden()
    {
        $model = $this->model();

        $expectedHidden = [
            'password',
            'remember_token',
            'email_verified_at',
            'lastest_token'
        ];

        $this->assertEquals(
            $expectedHidden,
            $model->getHidden()
        );
    }



}
