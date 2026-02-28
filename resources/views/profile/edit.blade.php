@php
    $layout = match(auth()->user()->role) {
        'admin'               => 'layouts.admin',
        'delivery_personnel'  => 'layouts.delivery',
        default               => 'layouts.warehouse',
    };
@endphp
@extends($layout)

@section('title', 'Edit Profile')
@section('page-title', 'Edit Profile')

@section('content')
<div class="profile-container">
    <div class="profile-header">
        <h2>Edit Profile</h2>
    </div>

    @if($errors->any())
        <div class="alert alert-error">
            <h4>Please fix the following errors:</h4>
            <ul>
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="profile-card">
        <form method="POST" action="{{ route('profile.update') }}" class="profile-form" enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="image-upload-section">
                <div class="image-preview" id="imagePreview">
                    @if($user->profile_image)
                        <img src="{{ $user->profile_image }}" alt="Profile">
                    @else
                        <svg width="60" height="60" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"></path>
                            <circle cx="12" cy="7" r="4"></circle>
                        </svg>
                    @endif
                </div>
                <input 
                    type="file" 
                    id="profile_image" 
                    name="profile_image" 
                    class="image-input @error('profile_image') is-invalid @enderror"
                    accept="image/*"
                    onchange="previewImage(event)">
                <label for="profile_image" class="upload-label">Choose Image</label>
                <span id="imageError" style="display:none;color:#e74c3c;font-size:0.82rem;margin-top:4px;"></span>
                <p class="upload-hint">JPG, PNG, or GIF (Max 2MB)</p>
                @error('profile_image')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-divider">Account Information</div>

            <div class="form-group">
                <label for="name">Full Name</label>
                <input 
                    type="text" 
                    id="name" 
                    name="name" 
                    class="form-control @error('name') is-invalid @enderror"
                    value="{{ old('name', $user->name) }}"
                    required>
                @error('name')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Email Address</label>
                <input 
                    type="email" 
                    id="email" 
                    name="email" 
                    class="form-control @error('email') is-invalid @enderror"
                    value="{{ old('email', $user->email) }}"
                    required>
                @error('email')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-divider">Change Password (Optional)</div>

            <div class="form-group">
                <label for="password">New Password</label>
                <input 
                    type="password" 
                    id="password" 
                    name="password" 
                    class="form-control @error('password') is-invalid @enderror"
                    placeholder="Leave blank to keep current password">
                @error('password')
                    <span class="form-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirm Password</label>
                <input 
                    type="password" 
                    id="password_confirmation" 
                    name="password_confirmation" 
                    class="form-control"
                    placeholder="Confirm new password">
            </div>

            <div class="form-actions">
                <button type="submit" class="btn-primary">Save Changes</button>
                <a href="{{ route('profile.show') }}" class="btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

<style>
:root {
    --primary-color: #2c3e50;
    --secondary-color: #3498db;
    --danger-color: #e74c3c;
    --light-gray: #ecf0f1;
    --border-color: #bdc3c7;
    --text-dark: #2c3e50;
    --text-light: #7f8c8d;
}

.profile-container {
    max-width: 600px;
    margin: 2rem auto;
    padding: 0 1.5rem;
}

.profile-header {
    margin-bottom: 2rem;
}

.profile-header h2 {
    margin: 0;
    font-size: 1.75rem;
    color: var(--text-dark);
}

.profile-card {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
    overflow: hidden;
}

.profile-form {
    padding: 2rem;
}

/* Image Upload Section */
.image-upload-section {
    display: flex;
    flex-direction: column;
    align-items: center;
    gap: 0.75rem;
    margin-bottom: 2rem;
    padding: 1.5rem;
    background: #f8f9fa;
    border: 2px dashed var(--light-gray);
    border-radius: 10px;
    text-align: center;
    transition: all 0.3s;
}

.image-upload-section:hover {
    border-color: var(--secondary-color);
    background: rgba(52, 152, 219, 0.05);
}

.image-preview {
    width: 150px;
    height: 150px;
    border-radius: 50%;
    background: linear-gradient(135deg, #e0e0e0 0%, #f5f5f5 100%);
    display: flex;
    align-items: center;
    justify-content: center;
    overflow: hidden;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
}

.image-preview img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    display: block;
}

.image-preview svg {
    color: #999;
    flex-shrink: 0;
}

.image-input {
    display: none;
}

.upload-label {
    padding: 0.6rem 1.2rem;
    background: var(--secondary-color);
    color: white;
    border-radius: 6px;
    cursor: pointer;
    font-weight: 600;
    font-size: 0.85rem;
    transition: all 0.3s;
    border: none;
}

.upload-label:hover {
    background: #2980b9;
    transform: translateY(-1px);
    box-shadow: 0 2px 8px rgba(52, 152, 219, 0.3);
}

.upload-hint {
    font-size: 0.8rem;
    color: var(--text-light);
    margin: 0;
}

/* Form Groups */
.form-group {
    margin-bottom: 1.5rem;
}

.form-group label {
    display: block;
    font-weight: 600;
    color: var(--text-dark);
    margin-bottom: 0.5rem;
    font-size: 0.95rem;
}

.form-control {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid var(--light-gray);
    border-radius: 8px;
    font-size: 0.95rem;
    transition: all 0.3s;
    background: white;
    font-family: inherit;
}

.form-control:focus {
    outline: none;
    border-color: var(--secondary-color);
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.form-control.is-invalid {
    border-color: var(--danger-color);
}

.form-control::placeholder {
    color: var(--text-light);
}

.form-error {
    display: block;
    color: var(--danger-color);
    font-size: 0.85rem;
    margin-top: 0.3rem;
}

.form-divider {
    padding: 1rem 0;
    margin: 2rem 0;
    border-top: 1px solid var(--light-gray);
    border-bottom: 1px solid var(--light-gray);
    font-weight: 600;
    color: var(--text-dark);
    font-size: 0.95rem;
    text-transform: uppercase;
    letter-spacing: 0.5px;
}

.form-actions {
    display: flex;
    gap: 1rem;
    margin-top: 2rem;
    padding-top: 2rem;
    border-top: 1px solid var(--light-gray);
}

.btn-primary,
.btn-secondary {
    padding: 0.75rem 1.5rem;
    border-radius: 8px;
    border: none;
    cursor: pointer;
    font-weight: 600;
    transition: all 0.3s;
    text-decoration: none;
    display: inline-block;
    font-size: 0.95rem;
}

.btn-primary {
    background: var(--secondary-color);
    color: white;
}

.btn-primary:hover {
    background: #2980b9;
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(52, 152, 219, 0.3);
}

.btn-secondary {
    background: var(--light-gray);
    color: var(--text-dark);
    border: 1px solid var(--border-color);
}

.btn-secondary:hover {
    background: var(--border-color);
    color: var(--primary-color);
}

.alert {
    padding: 1rem;
    border-radius: 8px;
    margin-bottom: 1.5rem;
}

.alert-error {
    background: #f8d7da;
    color: #721c24;
    border: 1px solid #f5c6cb;
}

.alert-error h4 {
    margin: 0 0 0.5rem 0;
    font-size: 1rem;
}

.alert-error ul {
    margin: 0;
    padding-left: 1.5rem;
}

.alert-error li {
    margin-bottom: 0.3rem;
}

@media (max-width: 576px) {
    .profile-container {
        padding: 0 0.75rem;
        margin: 1rem auto;
    }
    .profile-header h2 {
        font-size: 1.25rem;
    }
    .profile-form {
        padding: 1.25rem 1rem;
    }
    .image-preview {
        width: 100px;
        height: 100px;
    }
    .image-upload-section {
        padding: 1rem;
    }
    .form-group label {
        font-size: 0.85rem;
    }
    .form-control {
        font-size: 0.875rem;
        padding: 0.6rem 0.75rem;
    }
    .form-divider {
        font-size: 0.8rem;
        margin: 1.25rem 0;
        padding: 0.75rem 0;
    }
    .btn-primary,
    .btn-secondary {
        padding: 0.6rem 1rem;
        font-size: 0.85rem;
    }
    .form-actions {
        margin-top: 1.25rem;
        padding-top: 1.25rem;
    }
    .upload-hint {
        font-size: 0.75rem;
    }
    .upload-label {
        font-size: 0.8rem;
        padding: 0.5rem 1rem;
    }
}
</style>

<script>
function previewImage(event) {
    const file = event.target.files[0];
    const preview = document.getElementById('imagePreview');
    const errorEl = document.getElementById('imageError');

    if (file) {
        if (file.size > 2 * 1024 * 1024) {
            errorEl.textContent = 'Image must not exceed 2MB.';
            errorEl.style.display = 'block';
            event.target.value = '';
            return;
        }
        errorEl.style.display = 'none';
        const reader = new FileReader();
        
        reader.onload = function(e) {
            // Check if preview contains an image already
            let img = preview.querySelector('img');
            
            if (img) {
                // Update existing image
                img.src = e.target.result;
            } else {
                // Create new image and replace SVG
                img = document.createElement('img');
                img.src = e.target.result;
                img.alt = 'Profile';
                
                // Clear the preview and add the image
                preview.innerHTML = '';
                preview.appendChild(img);
            }
        };
        
        reader.readAsDataURL(file);
    }
}
</script>
@endsection
