<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Operator;
use App\Models\Role;
use App\Models\AlertStatus;
use Illuminate\Support\Carbon;

class MemberDashboardController extends Controller
{
    /**
     * Members’ landing page – my operational home screen.
     */
    public function __invoke()
    {
        // Load SignalSafe propagation brief from public/Condx/propagation-brief.json
        $condx = null;
        $path  = public_path('Condx/propagation-brief.json');

        if (file_exists($path)) {
            $json  = file_get_contents($path);
            $condx = json_decode($json, true);
        }

        // Small slice of upcoming events so this page always feels alive
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
            // Match role text on the operator to the defined role list
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

        // Current global alert status for banner/footer + members card
        $alertStatus = AlertStatus::query()->first();

        return view('pages.members', [
            'upcoming'      => $upcoming,
            'operator'      => $operator,
            'trainingLinks' => $trainingLinks,
            'resources'     => $resources,
            'opsSystems'    => $opsSystems,
            'alertStatus'   => $alertStatus,
            'condx'         => $condx,   // <-- now passed to the view
        ]);
    }
}
