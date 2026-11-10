@extends('layouts.public')

@section('title', 'Demo Users — LandscapingFollowFlow')

@section('content')
<section class="wrap section" style="padding-top:3rem;">
    <div class="eyebrow">Try it now</div>
    <h1>Demo users & roles</h1>
    <p class="lede">All demo passwords are <strong>password</strong>. Admin panel: <a href="/admin">/admin</a></p>
</section>

<section class="wrap section">
    <table>
        <thead>
            <tr>
                <th>Role</th>
                <th>Email</th>
                <th>Password</th>
                <th>Access</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>Admin</td><td>admin@landscapingfollowflow.test</td><td>password</td><td>Full platform</td></tr>
            <tr><td>Owner</td><td>owner@landscapingfollowflow.test</td><td>password</td><td>Approvals & escalations</td></tr>
            <tr><td>Staff</td><td>staff@landscapingfollowflow.test</td><td>password</td><td>Leads & follow-ups</td></tr>
            <tr><td>Technician</td><td>tech@landscapingfollowflow.test</td><td>password</td><td>Jobs & field view</td></tr>
            <tr><td>Customer</td><td>customer@landscapingfollowflow.test</td><td>password</td><td>Portal / demo stakeholder</td></tr>
        </tbody>
    </table>
</section>
@endsection
