<?php

namespace App\DB\Seeds;

use Phinx\Seed\AbstractSeed;

class UserTableSeeder extends AbstractSeed
{
    /**
     * Run Method.
     *
     * Write your database seeder using this method.
     *
     * More information on writing seeders is available here:
     * https://book.cakephp.org/phinx/0/en/seeding.html
     * @throws \Exception
     */
    public function run()
    {
        $faker = \Faker\Factory::create();
        for ($i = 0; $i < 10; $i++) {
            $password = \bin2hex(\random_bytes(10));
            $data[] = [
                'email' => $faker->email,
                'password' => \password_hash($password, PASSWORD_DEFAULT),
                'first_name' => $faker->firstName,
                'last_name' => $faker->lastName
            ];
        }
        $this->table('users')->insert($data)->saveData();
    }
}
