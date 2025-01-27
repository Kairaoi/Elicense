@extends('layouts.app')

@section('title', 'Target Species for Application')

@section('content')
    <div class="container">
        <h1 class="mb-4">Target Species for Application: {{ $application->application_id }}</h1>

        <!-- Display success message if any -->
        @if(session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif

        <!-- Display error message if any -->
        @if(session('error'))
            <div class="alert alert-danger">
                {{ session('error') }}
            </div>
        @endif

        <!-- Display target species table -->
        <div class="mb-3">
            <a href="{{ route('pfps.application-target-species.create', $application->application_id) }}" class="btn btn-primary">Add Target Species</a>
        </div>

        @if($targetSpecies->isEmpty())
            <p>No target species found for this application.</p>
        @else
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th scope="col">Species Name</th>
                        <th scope="col">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($targetSpecies as $target)
                        <tr>
                            <td>{{ $target->species->name ?? 'Unknown Species' }}</td> <!-- assuming 'name' is a field in the 'species' model -->
                            <td>
                                <!-- Delete button -->
                                <form action="{{ route('pfps.application-target-species.destroy', [$application->application_id, $target->species_id]) }}" method="POST" style="display:inline-block;">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger" onclick="return confirm('Are you sure you want to delete this target species?')">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        @endif
    </div>
@endsection
