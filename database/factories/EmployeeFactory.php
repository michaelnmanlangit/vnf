<?php

namespace Database\Factories;

use App\Models\Employee;
use Illuminate\Database\Eloquent\Factories\Factory;

class EmployeeFactory extends Factory
{
    protected $model = Employee::class;

    /**
     * Define the model's default state.
     */
    public function definition(): array
    {
        $departments = ['production', 'warehouse', 'delivery', 'administration', 'maintenance'];
        $warehousePositions = ['Inventory Officer', 'Temperature Technician', 'Payment Coordinator', 'Warehouse Manager', 'Warehouse Supervisor'];
        $deliveryPositions = ['Driver', 'Delivery Manager', 'Delivery Supervisor'];
        $otherPositions = ['Manager', 'Supervisor', 'Specialist', 'Operator', 'Assistant'];
        $statuses = ['active', 'inactive', 'on_leave'];

        $department = $this->faker->randomElement($departments);
        
        if ($department === 'warehouse') {
            $position = $this->faker->randomElement($warehousePositions);
        } elseif ($department === 'delivery') {
            $position = $this->faker->randomElement($deliveryPositions);
        } else {
            $position = $this->faker->randomElement($otherPositions);
        }

        return [
            'first_name' => $this->faker->firstName(),
            'last_name' => $this->faker->lastName(),
            'email' => $this->faker->unique()->safeEmail(),
            'phone' => $this->faker->phoneNumber(),
            'position' => $position,
            'department' => $department,
            'employment_status' => $this->faker->randomElement($statuses),
            'hire_date' => $this->faker->dateTimeBetween('-5 years', 'now'),
            'salary' => $this->faker->numberBetween(10000, 100000),
            'address' => $this->faker->address(),
            'image' => null,
        ];
    }
}
