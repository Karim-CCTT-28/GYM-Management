@extends("layouts.navigation")

@section("Management")
    <style>
        .management-wrapper {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 30px;
            width: 100%;
            height: 100%;
            min-height: 400px; 
            box-sizing: border-box;
            padding: 20px;
            flex-wrap: wrap; 
        }

        .management-card {
            background-color: #ffffff;
            width: 240px;
            height: 160px;
            border-radius: 12px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05), 0 1px 3px rgba(0, 0, 0, 0.1);
            border: 1px solid #edf2f7;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .management-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 20px rgba(0, 0, 0, 0.08);
            border-color: #e2e8f0;
        }

        .management-card a {
            text-decoration: none;
            color: black;
            font-size: 1.4rem;
            font-weight: 600;
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
    </style>

    <div class="management-wrapper">
        <div class="management-card">
            <a href="/employees">Employees</a> </div>

        <div class="management-card">
            <a href="/reports">Reports</a>
        </div>


        <div class="management-card">
            <a href="/subscription-types?q=1">Subscription Types</a>
        </div>

        <div class="management-card">
            <a href="/">Subscribers</a>
        </div>
    </div>
@endsection