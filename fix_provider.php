<?php
$file = 'g:/xampp/htdocs/class/public_html/app/Controllers/Provider.php';
$content = file_get_contents($file);

// Fix store rules
$oldStoreRules = "/'address'     => 'required',\s+'price'       => 'required\|numeric\|greater_than_equal_to\[0\]',/s";
$newStoreRules = "'address'     => 'required',
        ];

        if (\$this->request->getPost('type') !== 'regular') {
            \$rules['price'] = 'required|numeric|greater_than_equal_to[0]';
        }";
$content = preg_replace($oldStoreRules, $newStoreRules, $content, 1);

// Fix update rules
$oldUpdateRules = "/'address'            => 'required',\s+'price'              => 'required\|numeric\|greater_than_equal_to\[0\]',/s";
$newUpdateRules = "'address'            => 'required',
        ];

        if (\$this->request->getPost('type') !== 'regular') {
           \$rules['price'] = 'required|numeric|greater_than_equal_to[0]';
        }";
$content = preg_replace($oldUpdateRules, $newUpdateRules, $content, 1);

// Fix batch processing in store
$oldStoreBatch = "/if \(\$type === 'regular'\) \{\s+\$listingData\['start_date'\]      = \$this->request->getPost\('start_date'\) \?\: null;\s+\$listingData\['class_time'\]      = \$this->request->getPost\('class_time'\) \?\: null;\s+\$listingData\['class_end_time'\]  = \$this->request->getPost\('class_end_time'\) \?\: null;/s";
$newStoreBatch = "if (\$type === 'regular') {
                \$listingData['start_date']      = \$this->request->getPost('start_date') ?: null;
                \$listingData['class_time']      = null;
                \$listingData['class_end_time']  = null;

                \$batches = \$this->request->getPost('batches');
                if (!empty(\$batches) && is_array(\$batches)) {
                    \$batches = array_filter(\$batches, fn(\$b) => !empty(\$b['name']));
                    if (!empty(\$batches)) {
                        \$batches = array_values(\$batches);
                        \$listingData['batches'] = json_encode(\$batches);
                        
                        // Set base price from the cheapest batch for search/listing
                        \$prices = array_column(\$batches, 'price');
                        if (!empty(\$prices)) {
                            \$listingData['price'] = (float)min(\$prices);
                        }

                        // Use first batch time as default for legacy fields
                        \$listingData['class_time'] = \$batches[0]['from_time'] ?? null;
                        \$listingData['class_end_time'] = \$batches[0]['to_time'] ?? null;
                    }
                }";

$content = preg_replace($oldStoreBatch, $newStoreBatch, $content, 1);

// Fix batch processing in update
$oldUpdateBatch = "/if \(\$type === 'regular'\) \{\s+\$listingData\['start_date'\]      = \$this->request->getPost\('start_date'\) \?\: null;\s+\$listingData\['class_time'\]      = \$this->request->getPost\('class_time'\) \?\: null;\s+\$listingData\['class_end_time'\]  = \$this->request->getPost\('class_end_time'\) \?\: null;/s";
$newUpdateBatch = "if (\$type === 'regular') {
                \$listingData['start_date']      = \$this->request->getPost('start_date') ?: null;
                \$listingData['class_time']      = null;
                \$listingData['class_end_time']  = null;

                \$batches = \$this->request->getPost('batches');
                if (!empty(\$batches) && is_array(\$batches)) {
                    \$batches = array_filter(\$batches, fn(\$b) => !empty(\$b['name']));
                    if (!empty(\$batches)) {
                        \$batches = array_values(\$batches);
                        \$listingData['batches'] = json_encode(\$batches);
                        
                        // Set base price from the cheapest batch
                        \$prices = array_column(\$batches, 'price');
                        if (!empty(\$prices)) {
                            \$listingData['price'] = (float)min(\$prices);
                        }

                        // Legacy compatibility
                        \$listingData['class_time'] = \$batches[0]['from_time'] ?? null;
                        \$listingData['class_end_time'] = \$batches[0]['to_time'] ?? null;
                    }
                }";
$content = preg_replace($oldUpdateBatch, $newUpdateBatch, $content, 1);

file_put_contents($file, $content);
echo "Fixed successfully!";
