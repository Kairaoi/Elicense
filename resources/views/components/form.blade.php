@props(['action' => '#', 'method' => 'POST', 'title' => '', 'fields' => []])

<div class="container my-5">
    <h1 class="artistic-title">{{ $title }}</h1>
    <div class="artistic-form-container">
        <form action="{{ $action }}" method="{{ $method }}" enctype="multipart/form-data" class="artistic-form">
            @csrf

            @foreach($fields as $field)
                <div class="form-group">
                    <label for="{{ $field['name'] }}" class="artistic-label">{{ $field['label'] }}</label>

                    @switch($field['type'])
                        @case('select')
                            <select id="{{ $field['name'] }}" name="{{ $field['name'] }}" class="artistic-input" {{ $field['required'] ? 'required' : '' }}>
                                <option value="">Select {{ strtolower($field['label']) }}</option>
                                @foreach($field['options'] as $value => $label)
                                    <option value="{{ $value }}" {{ old($field['name']) == $value ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                            @break

                        @case('textarea')
                            <textarea id="{{ $field['name'] }}" name="{{ $field['name'] }}" class="artistic-input" {{ $field['required'] ? 'required' : '' }}>{{ old($field['name']) }}</textarea>
                            @break

                        @default
                            <input type="{{ $field['type'] }}" id="{{ $field['name'] }}" name="{{ $field['name'] }}" class="artistic-input" value="{{ old($field['name']) }}" {{ $field['required'] ? 'required' : '' }}>
                            @break
                    @endswitch

                    @error($field['name'])
                        <div class="text-danger mt-1">{{ $message }}</div>
                    @enderror
                </div>
            @endforeach

            <button type="submit" class="artistic-button">Submit</button>
        </form>
    </div>
</div>

@push('styles')
<style>
    @import url('https://fonts.googleapis.com/css2?family=Bebas+Neue&family=Roboto:wght@300;400;700&display=swap');

    :root {
        --primary-color: #FF6B6B;
        --secondary-color: #4ECDC4;
        --accent-color: #FFD93D;
        --bg-color: #F7F7F7;
        --text-color: #333333;
    }

    body {
        background-color: var(--bg-color);
        font-family: 'Roboto', sans-serif;
        color: var(--text-color);
        overflow-x: hidden;
    }

    .artistic-title {
        font-family: 'Bebas Neue', cursive;
        font-size: 5rem;
        color: var(--primary-color);
        text-transform: uppercase;
        letter-spacing: 5px;
        text-align: left;
        margin-bottom: 2rem;
        position: relative;
        z-index: 1;
    }

    .artistic-title::after {
        content: '';
        position: absolute;
        bottom: -10px;
        left: 0;
        width: 100px;
        height: 10px;
        background-color: var(--accent-color);
        transform: skew(-20deg);
    }

    .artistic-form-container {
        position: relative;
        padding: 3rem;
        background-color: white;
        box-shadow: 20px 20px 0 var(--secondary-color);
        transform: rotate(-2deg);
    }

    .artistic-form {
        transform: rotate(2deg);
    }

    .artistic-label {
        font-weight: 700;
        text-transform: uppercase;
        color: var(--secondary-color);
        margin-bottom: 0.5rem;
        display: block;
    }

    .artistic-input {
        width: 100%;
        padding: 10px;
        border: 2px solid var(--primary-color);
        background-color: transparent;
        color: var(--text-color);
        font-size: 1rem;
        transition: all 0.3s ease;
    }

    .artistic-input:focus {
        outline: none;
        border-color: var(--accent-color);
        box-shadow: 5px 5px 0 var(--primary-color);
    }

    .artistic-button {
        background-color: var(--primary-color);
        color: white;
        border: none;
        padding: 15px 30px;
        font-size: 1.2rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2px;
        cursor: pointer;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .artistic-button::before {
        content: '';
        position: absolute;
        top: 0;
        left: -100%;
        width: 100%;
        height: 100%;
        background: linear-gradient(120deg, transparent, rgba(255, 255, 255, 0.3), transparent);
        transition: all 0.4s ease;
    }

    .artistic-button:hover::before {
        left: 100%;
    }

    .artistic-button:hover {
        background-color: var(--secondary-color);
        transform: translate(5px, -5px);
        box-shadow: -5px 5px 0 var(--accent-color);
    }

    /* Decorative elements */
    .artistic-form-container::before,
    .artistic-form-container::after {
        content: '';
        position: absolute;
        background-color: var(--accent-color);
        z-index: -1;
    }

    .artistic-form-container::before {
        width: 100px;
        height: 100px;
        top: -20px;
        left: -20px;
        clip-path: polygon(0 0, 0% 100%, 100% 0);
    }

    .artistic-form-container::after {
        width: 150px;
        height: 150px;
        bottom: -20px;
        right: -20px;
        border-radius: 50%;
        opacity: 0.5;
    }

    @media (max-width: 768px) {
        .artistic-title {
            font-size: 3rem;
        }
        .artistic-form-container {
            padding: 2rem;
            box-shadow: 10px 10px 0 var(--secondary-color);
        }
    }
</style>
@endpush