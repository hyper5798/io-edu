<?php

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        /*$this->call(CpsTableSeeder::class);
        $this->call(NetworksTableSeeder::class);
        $this->call(RolesTableSeeder::class);
        $this->call(TypesTableSeeder::class);
        $this->call(UsersTableSeeder::class);
        $this->call(ReportsTableSeeder::class);
        $this->call(ClassOptionsTableSeeder::class);
        $this->call(ClassesTableSeeder::class);
        $this->call(RecordsTableSeeder::class);
        $this->call(TeamRecordsTableSeeder::class);
        $this->call(RoomsTableSeeder::class);
        $this->call(GamesTableSeeder::class);
        $this->call(MissionsTableSeeder::class);
        $this->call(TeamUserTableSeeder::class);
        $this->call(TeamsTableSeeder::class);
        $this->call(CommandsTableSeeder::class);
        $this->call(DevicesTableSeeder::class);
        $this->call(SettingsTableSeeder::class);
        $this->call(ProductsTableSeeder::class);
        $this->call(AppsTableSeeder::class);
        $this->call(FieldTableSeeder::class);
        $this->call(LevelTableSeeder::class);*/
        //$this->call(CategoriesTableSeeder::class);
        $this->call(CoursesTableSeeder::class);
        //$this->call(DropSeeder::class);
    }
}
