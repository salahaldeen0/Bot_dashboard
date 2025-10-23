<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\App;

class AppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data
        App::truncate();

        // Create sample apps
        $apps = [
            [
                'app_name' => 'E-Commerce Bot',
                'description' => 'AI-powered chatbot for online shopping assistance',
                'phone_number' => '+1-555-0101',
                'database_type' => 'mysql',
                'database_name' => 'ecommerce_bot_db',
                'port' => 3306,
                'host' => 'db1.ip-teamway.com',
                'username' => 'ecommerce_user',
                'password' => bcrypt('secure_password_123'),
            ],
            [
                'app_name' => 'Customer Support Bot',
                'description' => 'Automated customer service and support system',
                'phone_number' => '+1-555-0102',
                'database_type' => 'postgresql',
                'database_name' => 'support_bot_db',
                'port' => 5432,
                'host' => 'db2.ip-teamway.com',
                'username' => 'support_user',
                'password' => bcrypt('support_pass_456'),
            ],
            [
                'app_name' => 'Analytics Dashboard Bot',
                'description' => 'Real-time data analytics and reporting bot',
                'phone_number' => '+1-555-0103',
                'database_type' => 'mysql',
                'database_name' => 'analytics_db',
                'port' => 3306,
                'host' => 'db3.ip-teamway.com',
                'username' => 'analytics_user',
                'password' => bcrypt('analytics_789'),
            ],
            [
                'app_name' => 'Social Media Bot',
                'description' => 'Social media management and automation tool',
                'phone_number' => '+1-555-0104',
                'database_type' => 'mongodb',
                'database_name' => 'social_media_db',
                'port' => 27017,
                'host' => 'db4.ip-teamway.com',
                'username' => 'social_user',
                'password' => bcrypt('social_pass_321'),
            ],
            [
                'app_name' => 'Finance Bot',
                'description' => 'Personal finance tracking and advisory bot',
                'phone_number' => '+1-555-0105',
                'database_type' => 'postgresql',
                'database_name' => 'finance_bot_db',
                'port' => 5432,
                'host' => 'db5.ip-teamway.com',
                'username' => 'finance_user',
                'password' => bcrypt('finance_secure_654'),
            ],
            [
                'app_name' => 'Healthcare Assistant',
                'description' => 'Medical consultation and health monitoring bot',
                'phone_number' => '+1-555-0106',
                'database_type' => 'mysql',
                'database_name' => 'healthcare_db',
                'port' => 3306,
                'host' => 'db6.ip-teamway.com',
                'username' => 'health_user',
                'password' => bcrypt('health_pass_987'),
            ],
            [
                'app_name' => 'Education Bot',
                'description' => 'Learning management and tutoring system',
                'phone_number' => '+1-555-0107',
                'database_type' => 'sqlite',
                'database_name' => 'education_bot.db',
                'port' => 0,
                'host' => 'local',
                'username' => 'edu_user',
                'password' => bcrypt('education_123'),
            ],
            [
                'app_name' => 'Travel Assistant',
                'description' => 'Travel planning and booking assistance bot',
                'phone_number' => '+1-555-0108',
                'database_type' => 'mysql',
                'database_name' => 'travel_db',
                'port' => 3306,
                'host' => 'db7.ip-teamway.com',
                'username' => 'travel_user',
                'password' => bcrypt('travel_secure_456'),
            ],
        ];

        foreach ($apps as $app) {
            App::create($app);
        }
    }
}
