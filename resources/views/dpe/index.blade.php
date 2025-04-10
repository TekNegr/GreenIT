@extends('layouts.app')

@section('content')
<div class="px-4 py-4"> 
    <!-- Header Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-center mb-6">
        <!-- Title -->
        <h1 class="!text-3xl font-bold text-gray-800  !leading-tight">Liste des DPE</h1>

        <!-- Import Form -->
        <form action="{{ route('dpe.import') }}" method="POST" enctype="multipart/form-data" class="flex w-full justify-end">
            @csrf
            <div class="flex w-full md:w-auto">
                <input type="file" name="dpe_file" accept=".csv,.txt" required
                    class="border rounded-l py-2 px-4 w-full md:w-64">
                <button type="submit"
                    class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded-r whitespace-nowrap">
                    Importer
                </button>
            </div>
        </form>
    </div>


    <!-- Messages Section -->
    @if(session('success') || !empty(session('errors')))
    <div class="mb-6">
        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if(!empty(session('errors')))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul>
                    @foreach(session('errors') as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif
    </div>
    @endif

    <!-- Data Table Section -->
    <div class="bg-white shadow-md rounded overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr class="bg-gray-200 text-gray-600 uppercase text-sm leading-normal">
                    <th class="py-3 px-6 text-left">Numéro DPE</th>
                    <th class="py-3 px-6 text-left">Type</th>
                    <th class="py-3 px-6 text-left">Classe énergie</th>
                    <th class="py-3 px-6 text-left">Surface</th>
                    <th class="py-3 px-6 text-left">Localisation</th>
                </tr>
            </thead>
            <tbody class="text-gray-600 text-sm">
                @foreach($batiments as $batiment)
                <tr class="border-b border-gray-200 hover:bg-gray-100">
                    <td class="py-3 px-6">{{ $batiment->numero_dpe }}</td>
                    <td class="py-3 px-6">{{ $batiment->typeBatiment->libelle }}</td>
                    <td class="py-3 px-6">
                        <span class="px-2 py-1 rounded-full {{ $batiment->dpe_color }}">
                            {{ $batiment->classe_consommation_energie }}
                        </span>
                    </td>
                    <td class="py-3 px-6">{{ $batiment->surface_habitable }} m²</td>
                    <td class="py-3 px-6">
                        {{ $batiment->commune }} ({{ $batiment->departement->code }})
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4">
        {{ $batiments->links() }}
    </div>
</div>
@endsection
