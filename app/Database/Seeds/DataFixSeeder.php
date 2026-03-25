<?php

namespace App\Database\Seeds;

use CodeIgniter\Database\Seeder;

class DataFixSeeder extends Seeder
{
    public function run()
    {
        $db = \Config\Database::connect();
        
        $listings = $db->table('listings')->get()->getResultArray();
        $available_images = [
            'listing_16_cover.jpg',
            'listing_19_cover.jpg',
            'listing_22_cover.jpg',
            'listing_25_cover.jpg',
            'listing_28_cover.jpg'
        ];

        echo "Total listings: " . count($listings) . "\n";

        foreach ($listings as $listing) {
            $updates = [];
            echo "Processing listing ID: " . $listing['id'] . "\n";
            
            // 1. Fill missing images
            $imgCount = $db->table('listing_images')->where('listing_id', $listing['id'])->countAllResults();
            if ($imgCount == 0) {
                $img = $available_images[array_rand($available_images)];
                $db->table('listing_images')->insert([
                    'listing_id' => $listing['id'],
                    'image_path' => $img,
                    'position'   => 0
                ]);
                echo "  + Added cover image: $img\n";
            }
            
            // 2. Schedule & Payout-related Dates based on Type
            if ($listing['type'] === 'regular') {
                // Regular: usually ongoing. Start date could be when they joined.
                $updates['start_date']     = date('Y-m-d', strtotime('-1 month'));
                $updates['class_time']     = '17:00:00';
                $updates['class_end_time'] = '18:15:00';
                if (empty($listing['price_breakdown'])) {
                    $updates['price_breakdown'] = json_encode(['sessions' => 12, 'per_session' => round($listing['price'] / 12, 2)]);
                }
            } else if ($listing['type'] === 'workshop') {
                // Workshop: specific dates.
                $updates['start_date']     = date('Y-m-d', strtotime('+7 days'));
                $updates['end_date']       = date('Y-m-d', strtotime('+8 days')); // 2-day workshop
                $updates['class_time']     = '10:00:00';
                $updates['class_end_time'] = '14:00:00';
            } else if ($listing['type'] === 'course') {
                // Course: long term (2-3 months).
                $updates['start_date']     = date('Y-m-d', strtotime('+14 days'));
                $updates['end_date']       = date('Y-m-d', strtotime('+104 days')); // 3 months
                $updates['class_time']     = '16:00:00';
                $updates['class_end_time'] = '17:30:00';
            }
            
            // 3. Ensure they are active and approved so they appear in listings
            $updates['status'] = 'active';
            $updates['review_status'] = 'approved';
            
            if (!empty($updates)) {
                $db->table('listings')->where('id', $listing['id'])->update($updates);
                echo "  + Updated schedule and status\n";
            }
        }
    }
}
