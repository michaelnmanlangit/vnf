<?php

namespace Database\Seeders;

use App\Models\Customer;
use Illuminate\Database\Seeder;

class CustomerSeeder extends Seeder
{
    /**
     * Run the database seeder to create sample customers.
     */
    public function run(): void
    {
        $customers = [
            // Restaurants
            [
                'business_name' => 'The Golden Fork Restaurant',
                'contact_person' => 'John Martinez',
                'email' => 'john@goldenfork.com',
                'phone' => '555-0101',
                'address' => '123 Main Street, New York, NY 10001',
                'customer_type' => 'restaurant',
                'status' => 'active',
                'notes' => 'Regular customer, prefers weekly deliveries'
            ],
            [
                'business_name' => 'Ocean Breeze Cafe',
                'contact_person' => 'Maria Garcia',
                'email' => 'maria@oceanbreeze.com',
                'phone' => '555-0102',
                'address' => '456 Beach Avenue, Miami, FL 33101',
                'customer_type' => 'restaurant',
                'status' => 'active',
                'notes' => 'Specializes in seafood, high volume orders'
            ],
            [
                'business_name' => 'Steakhouse Prime',
                'contact_person' => 'Robert Lee',
                'email' => 'robert@steakhouseprime.com',
                'phone' => '555-0103',
                'address' => '789 Park Lane, Chicago, IL 60601',
                'customer_type' => 'restaurant',
                'status' => 'active',
                'notes' => 'Premium beef supplier, VIP customer'
            ],
            
            // Meat Suppliers
            [
                'business_name' => 'Premium Meat Wholesalers',
                'contact_person' => 'Sarah Johnson',
                'email' => 'sarah@premeatwhole.com',
                'phone' => '555-0201',
                'address' => '321 Wholesale Avenue, Los Angeles, CA 90001',
                'customer_type' => 'meat_supplier',
                'status' => 'active',
                'notes' => '5-star supplier, bulk orders for distribution'
            ],
            [
                'business_name' => 'Quality Meat Distributor',
                'contact_person' => 'Michael Chen',
                'email' => 'michael@qualitymeat.com',
                'phone' => '555-0202',
                'address' => '654 Industrial Street, Boston, MA 02101',
                'customer_type' => 'meat_supplier',
                'status' => 'active',
                'notes' => 'Regular supplier, consistent orders'
            ],
            
            // Fishery
            [
                'business_name' => 'Fresh Catch Fishery',
                'contact_person' => 'Angela White',
                'email' => 'angela@freshcatch.com',
                'phone' => '555-0301',
                'address' => '987 Dock Way, San Francisco, CA 94101',
                'customer_type' => 'fishery',
                'status' => 'active',
                'notes' => 'Sea products, seasonal variations'
            ],
            [
                'business_name' => 'Coastal Seafood Farms',
                'contact_person' => 'David Brown',
                'email' => 'david@coastalseafood.com',
                'phone' => '555-0302',
                'address' => '258 Marine Plaza, Houston, TX 77001',
                'customer_type' => 'fishery',
                'status' => 'active',
                'notes' => 'Aquaculture products'
            ],
            
            // Grocery Stores
            [
                'business_name' => 'Fresh Market Supermarket',
                'contact_person' => 'Lisa Park',
                'email' => 'lisa@freshmarket.com',
                'phone' => '555-0401',
                'address' => '741 Shopping Center, Phoenix, AZ 85001',
                'customer_type' => 'grocery',
                'status' => 'active',
                'notes' => 'Frozen foods section supplier'
            ],
            [
                'business_name' => 'Value Mart Stores',
                'contact_person' => 'James Wilson',
                'email' => 'james@valuemart.com',
                'phone' => '555-0402',
                'address' => '852 Retail Row, Philadelphia, PA 19101',
                'customer_type' => 'grocery',
                'status' => 'active',
                'notes' => 'Chain of 20 stores, distribution required'
            ],
            
            // Distribution Companies
            [
                'business_name' => 'Express Food Distribution',
                'contact_person' => 'Thomas Gray',
                'email' => 'thomas@expressfood.com',
                'phone' => '555-0501',
                'address' => '369 Industrial Drive, Detroit, MI 48201',
                'customer_type' => 'distribution_company',
                'status' => 'active',
                'notes' => 'Bulk distribution to retailers'
            ],
            [
                'business_name' => 'Nationwide Logistics Foods',
                'contact_person' => 'Patricia Davis',
                'email' => 'patricia@nationwidelogi.com',
                'phone' => '555-0502',
                'address' => '741 Manufacturing Lane, San Antonio, TX 78201',
                'customer_type' => 'distribution_company',
                'status' => 'active',
                'notes' => 'Multi-state distribution network'
            ],

            // Wet Market
            [
                'business_name' => 'Central City Wet Market',
                'contact_person' => 'Vincent Lopez',
                'email' => 'vincent@centralwetmarket.com',
                'phone' => '555-0601',
                'address' => '500 Market Street, Seattle, WA 98101',
                'customer_type' => 'wet_market',
                'status' => 'active',
                'notes' => 'Local retail wet market operator'
            ],
        ];

        foreach ($customers as $customer) {
            Customer::create($customer);
            $this->command->info("Added customer: {$customer['business_name']}");
        }

        $this->command->info('Customer seeding completed successfully!');
    }
}
