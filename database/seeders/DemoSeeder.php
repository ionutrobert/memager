<?php

namespace Database\Seeders;

use App\Models\Debt;
use App\Models\ContactInfo;
use App\Models\DiscutieTelefonica;
use App\Models\Member;
use App\Models\MemberWorkplaceDetail;
use App\Models\Nota;
use App\Models\Payment;
use App\Models\PreviousIdentity;
use App\Models\User;
use App\Models\Workplace;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;

class DemoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing demo data if any, to ensure a fresh seed
        // (Optional, consider if you want to run this multiple times)
        // Debt::query()->delete();
        // DiscutieTelefonica::query()->delete();
        // MemberWorkplaceDetail::query()->delete();
        // Workplace::query()->delete();
        // Nota::query()->delete();
        // PreviousIdentity::query()->delete();
        // Member::query()->delete();
        // User::where('email', '!=', 'admin@admin.com')->delete(); // Keep main admin

        // 1. Create Demo Users (Admins with different roles)
        $superAdminRole = Role::firstOrCreate(['name' => 'super_admin', 'guard_name' => 'web']);
        $adminRole      = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $managerRole    = Role::firstOrCreate(['name' => 'manager', 'guard_name' => 'web']);
        $viewerRole     = Role::firstOrCreate(['name' => 'viewer', 'guard_name' => 'web']);

        $demoUsers = [];

        $demoUsers[] = User::firstOrCreate(
            ['email' => 'superadmin@example.com'],
            ['name' => 'Demo SuperAdmin', 'password' => Hash::make('password')]
        )->assignRole($superAdminRole);

        $demoUsers[] = User::firstOrCreate(
            ['email' => 'admin@example.com'],
            ['name' => 'Demo Admin', 'password' => Hash::make('password')]
        )->assignRole($adminRole);

        $demoUsers[] = User::firstOrCreate(
            ['email' => 'manager@example.com'],
            ['name' => 'Demo Manager', 'password' => Hash::make('password')]
        )->assignRole($managerRole);

        $demoUsers[] = User::firstOrCreate(
            ['email' => 'viewer@example.com'],
            ['name' => 'Demo Viewer', 'password' => Hash::make('password')]
        )->assignRole($viewerRole);

        // 2. Create Members with relationships
        Member::factory(5)->create()->each(function (Member $member) use ($demoUsers) {
            // Assign a random user to the member
            $member->user_id = $demoUsers[array_rand($demoUsers)]->id;
            $member->save();

            // Previous Identities
            PreviousIdentity::factory(rand(1, 3))->create([
                'member_id' => $member->id,
            ]);

            // Contact infos (phone/email/address) - create one of each type
            $contacts = collect([
                ContactInfo::factory()->telefon()->create([
                    'member_id' => $member->id,
                    'user_id'   => $demoUsers[array_rand($demoUsers)]->id,
                ]),
                ContactInfo::factory()->email()->create([
                    'member_id' => $member->id,
                    'user_id'   => $demoUsers[array_rand($demoUsers)]->id,
                ]),
                ContactInfo::factory()->adresa()->create([
                    'member_id' => $member->id,
                    'user_id'   => $demoUsers[array_rand($demoUsers)]->id,
                ]),
            ]);

            // Discutii Telefonice (Phone Discussions) - create unsaved models then assign required foreign keys and save
            $count = rand(1, 5);
            $discussions = DiscutieTelefonica::factory()->count($count)->make();
            foreach ($discussions as $d) {
                $d->member_id = $member->id;
                $d->contact_info_id = $contacts->random()->id;
                $d->user_id = $demoUsers[array_rand($demoUsers)]->id;
                $d->save();
            }

            // Notes
            Nota::factory(rand(1, 4))->create([
                'member_id' => $member->id,
                'user_id'   => $demoUsers[array_rand($demoUsers)]->id,
            ]);

            // Workplaces with details: create workplaces, attach pivot, and add member-specific details
            $wpCount = rand(1, 2);
            for ($i = 0; $i < $wpCount; $i++) {
                $wp = Workplace::factory()->create([
                    'user_id' => $demoUsers[array_rand($demoUsers)]->id,
                ]);
                $member->workplaces()->attach($wp->id);
                MemberWorkplaceDetail::factory(1)->create([
                    'workplace_id' => $wp->id,
                    'member_id'    => $member->id,
                    'user_id'      => $demoUsers[array_rand($demoUsers)]->id,
                ]);
            }

            // Debts and Payments
            Debt::factory(rand(1, 3))->create([
                'member_id' => $member->id,
                'user_id'   => $demoUsers[array_rand($demoUsers)]->id,
            ])->each(function (Debt $debt) {
                Payment::factory(rand(1, 5))->create([
                    'debt_id' => $debt->id,
                    'user_id' => $debt->user_id, // Use the same user who created the debt
                ]);
            });
        });
    }
}
