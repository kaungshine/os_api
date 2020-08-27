<?php

use Illuminate\Database\Seeder;
use App\Subcategory;
use App\Brand;
use App\Item;

class ItemTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        //
        factory(App\Item::class, 3)->create();
    }
}
