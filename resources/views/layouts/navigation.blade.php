<nav class="bg-slate-950 border-b border-slate-800">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16 items-center">

            <div class="flex items-center space-x-3">
                <a href="{{ route('home') }}" class="text-sky-400 font-semibold tracking-wide">
                    Liverpool RAYNET
                </a>
            </div>

            <div class="hidden md:flex space-x-6 text-sm">
                <a href="{{ route('home') }}" class="hover:text-sky-300">Home</a>
                <a href="{{ route('about') }}" class="hover:text-sky-300">About</a>
                <a href="{{ route('event-support') }}" class="hover:text-sky-300">Event support</a>
                <a href="{{ route('training') }}" class="hover:text-sky-300">Training</a>
                <a href="{{ route('events.index') }}" class="hover:text-sky-300">Events</a>
            </div>

            <div class="flex items-center space-x-3 text-sm">
                @auth
                    <a href="{{ route('members') }}"
                       class="px-3 py-1 rounded-full bg-sky-600 hover:bg-sky-500 text-white">
                        Members’ hub
                    </a>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                                class="px-3 py-1 rounded-full border border-slate-500 hover:bg-slate-800">
                            Log out
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}"
                       class="px-3 py-1 rounded-full bg-sky-600 hover:bg-sky-500 text-white">
                        Member login
                    </a>
                @endauth
            </div>
        </div>
    </div>
</nav>