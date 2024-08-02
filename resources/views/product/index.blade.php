<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Product') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (auth()->user()->is_admin)
                        <a href="{{ route('products.create') }}">
                            Add New Product
                        </a>
                    @endif
                    <table>
                        @forelse ($datas as $item)
                            <tr>
                                <td>
                                    <a href="{{ route('products.edit', $item->id) }}">Edit</a>
                                </td>
                                <td>{{ $item->name }}</td>
                                <td>{{ $item->price }}</td>
                            </tr>
                        @empty
                        {{ __("Nothing in here") }}
                        @endforelse
                    </table>
                    {{ $datas->links() }}
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
