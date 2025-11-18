<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Operator;
use App\Models\Role;
use Illuminate\Support\Carbon;

class MemberDashboardController extends Controller
{
    /**
     * Members’ landing page – my operational home screen.
     */
    public function __invoke()
    {
        // I want a small slice of upcoming events so this page always feels alive
        $upcoming = Event::with('type')
            ->where('starts_at', '>=', Carbon::today()->startOfDay())
            ->orderBy('starts_at')
            ->limit(6)
            ->get();

        // For now I’ll just use the first operator record (biased to admins)
        $operatorModel = Operator::orderByDesc('is_admin')
            ->orderBy('name')
            ->first();

        $roleModel = null;
        if ($operatorModel && $operatorModel->role) {
            // I’m matching role text on the operator to the defined role list
            $roleModel = Role::where('name', $operatorModel->role)->first();
        }

        $operator = [
            'name'        => $operatorModel?->name ?? 'Operator',
            'callsign'    => $operatorModel?->callsign ?? '',
            'role'        => $operatorModel?->role ?? 'Member',
            'level'       => $operatorModel?->level ?? 'Level: unknown',
            'status'      => $operatorModel?->status ?? 'Status unknown',
            'role_colour' => $roleModel?->colour, // can be null – view will handle fallback
        ];

        // Training curriculum + learning routes I want quick access to
        $trainingLinks = [
            [
                'label' => 'Level 0–2 operator pathway',
                'url'   => 'https://raynet-training.uk/pathways/level-0-2',
            ],
            [
                'label' => 'Specialist modules (NVIS, Power, Digital)',
                'url'   => 'https://raynet-training.uk/modules',
            ],
            [
                'label' => 'Assessment & sign-off forms',
                'url'   => 'https://raynet-training.uk/forms',
            ],
        ];

        // Frequency plans, SOPs, and other core references
        $resources = [
            [
                'label' => 'Merseyside VHF/UHF frequency plan (PDF)',
                'url'   => '/docs/frequency-plan-merseyside.pdf',
            ],
            [
                'label' => 'Local SOPs and checklists (PDF bundle)',
                'url'   => '/docs/liverpool-raynet-sops.pdf',
            ],
            [
                'label' => 'Net times & regular skeds',
                'url'   => '/docs/net-times.pdf',
            ],
            [
                'label' => 'RAYNET-UK members’ area',
                'url'   => 'https://members.raynet-uk.net/',
            ],
        ];

        // Hooks into self-hosted systems – I can change these URLs later
        $opsSystems = [
            'ops_board_url' => 'https://ops.liverpool.ray-net.uk',     // placeholder
            'backend_url'   => 'https://backend.liverpool.ray-net.uk', // placeholder
        ];

        return view('pages.members', compact(
            'upcoming',
            'operator',
            'trainingLinks',
            'resources',
            'opsSystems'
        ));
    }
}