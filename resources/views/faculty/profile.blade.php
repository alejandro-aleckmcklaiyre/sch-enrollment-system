@extends('layouts.app')

@section('title', 'Faculty Profile')

@section('content')
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 16px;">
    <h1 style="margin: 0; font-size: 1.5em;">Faculty Profile</h1>
</div>

@if(session('success'))
    <div style="background-color: #d4edda; border: 1px solid #c3e6cb; color: #155724; padding: 12px; border-radius: 4px; margin-bottom: 16px;">
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div style="background-color: #f8d7da; border: 1px solid #f5c6cb; color: #721c24; padding: 12px; border-radius: 4px; margin-bottom: 16px;">
        {{ session('error') }}
    </div>
@endif

<!-- Tabs Navigation -->
<div style="display: flex; gap: 0; border-bottom: 2px solid #c4b59f; margin-bottom: 24px; background: white; border-radius: 4px 4px 0 0;">
    <button onclick="switchTab('profile')" id="profile-tab" style="flex: 1; padding: 12px 16px; background: var(--accent); color: white; border: none; font-weight: 600; cursor: pointer; border-radius: 4px 0 0 0; text-align: left;">
        <i class="fas fa-user" style="margin-right: 8px;"></i> Profile Information
    </button>
    <button onclick="switchTab('security')" id="security-tab" style="flex: 1; padding: 12px 16px; background: transparent; color: var(--text); border: none; font-weight: 600; cursor: pointer; text-align: left;">
        <i class="fas fa-lock" style="margin-right: 8px;"></i> Security & Password
    </button>
</div>

<!-- Profile Tab -->
<div id="profile-content" style="display: block;">
    <div style="background: white; padding: 20px; border-radius: 4px; box-shadow: 0 1px 0 rgba(0,0,0,0.03);">
        @if($instructor)
            <h3 style="margin: 0 0 16px 0; color: #2e2a26;">Your Profile Information</h3>
            
            <form action="{{ route('faculty.profile.update') }}" method="POST">
                @csrf
                @method('PUT')
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                    <div>
                        <label style="display: block; margin-bottom: 6px; font-weight: 500; color: var(--text);">First Name</label>
                        <input type="text" name="first_name" value="{{ old('first_name', $instructor->first_name ?? '') }}" 
                               style="width: 100%; padding: 8px 12px; border: 1px solid #c4b59f; border-radius: 4px; font-size: 0.95em; box-sizing: border-box;">
                        @error('first_name')
                            <small style="color: #dc3545;">{{ $message }}</small>
                        @enderror
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 6px; font-weight: 500; color: var(--text);">Last Name</label>
                        <input type="text" name="last_name" value="{{ old('last_name', $instructor->last_name ?? '') }}" 
                               style="width: 100%; padding: 8px 12px; border: 1px solid #c4b59f; border-radius: 4px; font-size: 0.95em; box-sizing: border-box;">
                        @error('last_name')
                            <small style="color: #dc3545;">{{ $message }}</small>
                        @enderror
                    </div>
                </div>
                
                <div style="margin-bottom: 16px;">
                    <label style="display: block; margin-bottom: 6px; font-weight: 500; color: var(--text);">Email</label>
                    <input type="email" name="email" value="{{ old('email', $instructor->email ?? '') }}" 
                           style="width: 100%; padding: 8px 12px; border: 1px solid #c4b59f; border-radius: 4px; font-size: 0.95em; box-sizing: border-box;">
                    @error('email')
                        <small style="color: #dc3545;">{{ $message }}</small>
                    @enderror
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 6px; font-weight: 500; color: var(--text);">Instructor ID</label>
                        <input type="text" value="{{ $instructor->instructor_id ?? 'N/A' }}" disabled 
                               style="width: 100%; padding: 8px 12px; border: 1px solid #c4b59f; border-radius: 4px; font-size: 0.95em; box-sizing: border-box; background-color: #f5f5f5; color: #666;">
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 6px; font-weight: 500; color: var(--text);">Department</label>
                        <input type="text" value="{{ $instructor->department->dept_name ?? 'Not assigned' }}" disabled 
                               style="width: 100%; padding: 8px 12px; border: 1px solid #c4b59f; border-radius: 4px; font-size: 0.95em; box-sizing: border-box; background-color: #f5f5f5; color: #666;">
                    </div>
                </div>
                
                <div style="display: flex; gap: 8px;">
                    <button type="submit" style="background: var(--accent); color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; font-weight: 500;">
                        <i class="fas fa-save" style="margin-right: 6px;"></i> Save Profile
                    </button>
                    <a href="{{ route('faculty.dashboard') }}" style="display: inline-block; background: var(--muted); color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; cursor: pointer;">Back to Dashboard</a>
                </div>
            </form>
        @else
            <div style="background-color: #e2e3e5; border: 1px solid #d3d3d3; color: #383d41; padding: 16px; border-radius: 4px;">
                <h4 style="margin: 0 0 8px 0;">Create Your Profile</h4>
                <p style="margin: 0 0 16px 0;">Your profile has not been set up yet. Please complete the form below to create your instructor profile.</p>
                
                <form action="{{ route('faculty.profile.update') }}" method="POST">
                    @csrf
                    @method('PUT')
                    
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                        <div>
                            <label style="display: block; margin-bottom: 6px; font-weight: 500; color: var(--text);">First Name *</label>
                            <input type="text" name="first_name" value="{{ old('first_name', '') }}" required 
                                   style="width: 100%; padding: 8px 12px; border: 1px solid #c4b59f; border-radius: 4px; font-size: 0.95em; box-sizing: border-box;">
                            @error('first_name')
                                <small style="color: #dc3545;">{{ $message }}</small>
                            @enderror
                        </div>
                        
                        <div>
                            <label style="display: block; margin-bottom: 6px; font-weight: 500; color: var(--text);">Last Name *</label>
                            <input type="text" name="last_name" value="{{ old('last_name', '') }}" required 
                                   style="width: 100%; padding: 8px 12px; border: 1px solid #c4b59f; border-radius: 4px; font-size: 0.95em; box-sizing: border-box;">
                            @error('last_name')
                                <small style="color: #dc3545;">{{ $message }}</small>
                            @enderror
                        </div>
                    </div>
                    
                    <div style="margin-bottom: 16px;">
                        <label style="display: block; margin-bottom: 6px; font-weight: 500; color: var(--text);">Email</label>
                        <input type="email" name="email" value="{{ old('email', auth()->user()->email) }}" 
                               style="width: 100%; padding: 8px 12px; border: 1px solid #c4b59f; border-radius: 4px; font-size: 0.95em; box-sizing: border-box;">
                        @error('email')
                            <small style="color: #dc3545;">{{ $message }}</small>
                        @enderror
                    </div>
                    
                    <div style="display: flex; gap: 8px;">
                        <button type="submit" style="background: var(--accent); color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; font-weight: 500;">
                            <i class="fas fa-plus" style="margin-right: 6px;"></i> Create Profile
                        </button>
                        <a href="{{ route('faculty.dashboard') }}" style="display: inline-block; background: var(--muted); color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; cursor: pointer;">Back to Dashboard</a>
                    </div>
                </form>
            </div>
        @endif
    </div>
</div>

<!-- Security Tab -->
<div id="security-content" style="display: none;">
    <div style="background: white; padding: 20px; border-radius: 4px; box-shadow: 0 1px 0 rgba(0,0,0,0.03);">
        <h3 style="margin: 0 0 16px 0; color: #2e2a26;">Change Password</h3>
        
        <form action="{{ route('faculty.profile.update-password') }}" method="POST">
            @csrf
            @method('PUT')
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 500; color: var(--text);">Current Password</label>
                <input type="password" name="current_password" 
                       style="width: 100%; padding: 8px 12px; border: 1px solid #c4b59f; border-radius: 4px; font-size: 0.95em; box-sizing: border-box;">
                @error('current_password')
                    <small style="color: #dc3545;">{{ $message }}</small>
                @enderror
            </div>
            
            <div style="margin-bottom: 16px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 500; color: var(--text);">New Password</label>
                <input type="password" name="password" 
                       style="width: 100%; padding: 8px 12px; border: 1px solid #c4b59f; border-radius: 4px; font-size: 0.95em; box-sizing: border-box;">
                @error('password')
                    <small style="color: #dc3545;">{{ $message }}</small>
                @enderror
            </div>
            
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 6px; font-weight: 500; color: var(--text);">Confirm Password</label>
                <input type="password" name="password_confirmation" 
                       style="width: 100%; padding: 8px 12px; border: 1px solid #c4b59f; border-radius: 4px; font-size: 0.95em; box-sizing: border-box;">
            </div>
            
            <div style="display: flex; gap: 8px;">
                <button type="submit" style="background: var(--accent); color: white; padding: 8px 16px; border: none; border-radius: 4px; cursor: pointer; font-weight: 500;">
                    <i class="fas fa-shield-alt" style="margin-right: 6px;"></i> Update Password
                </button>
                <a href="{{ route('faculty.dashboard') }}" style="display: inline-block; background: var(--muted); color: white; padding: 8px 16px; border-radius: 4px; text-decoration: none; cursor: pointer;">Back to Dashboard</a>
            </div>
        </form>
    </div>
</div>

<script>
function switchTab(tab) {
    // Hide all content
    document.getElementById('profile-content').style.display = 'none';
    document.getElementById('security-content').style.display = 'none';
    
    // Remove active state from all tabs
    document.getElementById('profile-tab').style.background = 'transparent';
    document.getElementById('profile-tab').style.color = 'var(--text)';
    document.getElementById('security-tab').style.background = 'transparent';
    document.getElementById('security-tab').style.color = 'var(--text)';
    
    // Show selected content
    document.getElementById(tab + '-content').style.display = 'block';
    
    // Set active state on clicked tab
    document.getElementById(tab + '-tab').style.background = 'var(--accent)';
    document.getElementById(tab + '-tab').style.color = 'white';
}
</script>
@endsection
