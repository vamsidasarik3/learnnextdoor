<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run()
    {
        $data = [
            [
                'name' => 'Sports & Fitness',
                'icon' => 'bi-trophy-fill',
                'subcategories' => [
                    'Self Defence', 'Yoga', 'Karate Class', 'Martial Arts', 'Cricket', 'Swimming', 
                    'Tennis', 'Football', 'Badminton', 'Basketball', 'Table Tennis', 'Skating', 
                    'Rhythmic Gymnastics', 'Zumba', 'Aerobics', 'Hula Hoop', 'Chess', 'Others'
                ]
            ],
            [
                'name' => 'Music & Performing Arts',
                'icon' => 'bi-music-note-beamed',
                'subcategories' => [
                    'Singing', 'Guitar', 'Piano', 'Ukulele', 'Drums', 'Western Dance', 
                    'Classical Dance', 'Acting / Theatre', 'RJ (Radio Jockey)', 'DJ Classes', 'Others'
                ]
            ],
            [
                'name' => 'Academic Support',
                'icon' => 'bi-book-fill',
                'subcategories' => [
                    'All Subjects', 'Maths', 'English', 'Hindi', 'Science', 'Physics', 'Chemistry', 
                    'Biology', 'Economics', 'History', 'Political Science', 'Geography', 'French', 
                    'German', 'Sanskrit', 'Others'
                ]
            ],
            [
                'name' => 'Brain Development & Competitive Skills',
                'icon' => 'bi-lightbulb-fill',
                'subcategories' => [
                    'Abacus', 'Vedic Maths', 'Mental Maths', 'Speed Math', 'Olympiad Prep', 
                    'Logical Reasoning', 'Others'
                ]
            ],
            [
                'name' => 'Art, Craft & Creativity',
                'icon' => 'bi-palette-fill',
                'subcategories' => [
                    'Drawing', 'Painting', 'Calligraphy', 'Resin Art', 'Quilting', 
                    'Best out of Waste', 'DIY Crafts', 'Sculpture', 'Others'
                ]
            ],
            [
                'name' => 'Life Skills & Personality Development',
                'icon' => 'bi-person-badge-fill',
                'subcategories' => [
                    'Public Speaking', 'Language Classes', 'Personality Development', 'Debate', 
                    'Communication Skills', 'Leadership', 'Others'
                ]
            ],
            [
                'name' => 'Cooking & Culinary',
                'icon' => 'bi-egg-fried',
                'subcategories' => [
                    'Baking', 'Healthy Cooking', 'Kids Cooking', 'Others'
                ]
            ],
            [
                'name' => 'Technology & Coding',
                'icon' => 'bi-laptop',
                'subcategories' => [
                    'Coding for Kids', 'Robotics', 'AI for Kids', 'Game Development', 
                    'App Development', 'Others'
                ]
            ],
        ];

        $db = \Config\Database::connect();
        
        // Disable foreign key checks to allow truncation if needed (migration already did it but good to be safe)
        $db->query('SET FOREIGN_KEY_CHECKS = 0;');
        $db->table('subcategories')->emptyTable();
        $db->table('categories')->emptyTable();
        $db->query('SET FOREIGN_KEY_CHECKS = 1;');

        foreach ($data as $catData) {
            $subcategories = $catData['subcategories'];
            unset($catData['subcategories']);
            
            $catData['status'] = 'active';
            $db->table('categories')->insert($catData);
            $categoryId = $db->insertID();

            foreach ($subcategories as $subName) {
                $db->table('subcategories')->insert([
                    'category_id' => $categoryId,
                    'name'        => $subName,
                    'slug'        => url_title($subName, '-', true),
                    'status'      => 'active'
                ]);
            }
        }
    }
}
