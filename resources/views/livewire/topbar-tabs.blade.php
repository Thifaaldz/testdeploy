<div class="flex items-center h-full ml-4">
    <div class="flex items-center h-full">
        @php
            $tabs = ['DSI', 'IBS', 'IMK', 'KEK-KI'];
        @endphp
        @foreach($tabs as $tab)
            <button 
                wire:click="setTab('{{ $tab }}')"
                class="px-6 h-full font-bold text-sm transition-all duration-200
                    {{ $activeTab === $tab 
                        ? 'bg-white text-gray-900 shadow-sm border-b-2 border-[#E07B2A]' 
                        : 'bg-transparent text-gray-700 hover:bg-yellow-300/40' }}"
            >
                {{ $tab }}
            </button>
        @endforeach
    </div>
</div>
