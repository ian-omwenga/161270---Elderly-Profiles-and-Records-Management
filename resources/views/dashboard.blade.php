<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Family Overview | ElderCare</title>

    <link rel="stylesheet" href="{{ asset('css/dashboard.css') }}">
</head>

<body>

<div class="app-container">

    <!-- =========================

    <header class="topbar">

        <div class="breadcrumb">
            <span>Dashboard</span>
            <span class="separator">/</span>
            <span>Family Overview</span>
        </div>

        <div class="topbar-right">

            <div class="search-box">
                <span>⌕</span>
                <input type="text" placeholder="Search records, patients...">
            </div>

            <button class="notification-btn">
                
                <span class="notification-dot"></span>
            </button>

            <div class="user-menu">
                <div class="small-avatar">U</div>

                <div class="user-menu-text">
                    <strong>User</strong>
                    <small>Family Member</small>
                </div>

                <span class="dropdown-arrow">⌄</span>
            </div>

        </div>

    </header>


    <!-- =========================
         MAIN CONTENT
    ========================== -->
    <main class="dashboard">

        <!-- =========================
             ELDER PROFILE HEADER
        ========================== -->
        <section class="profile-card">

            <div class="profile-left">

                <div class="profile-avatar">
                    U
                    <span class="online-status"></span>
                </div>

                <div class="profile-info">

                    <h1>User</h1>

                    <span class="patient-id">
                        Patient ID: ELR-5421
                    </span>

                    <p>
                        Currently residing at Home Care Unit.
                        Latest health status shows stable mood,
                        with slight variations in appetite over the
                        past 48 hours.
                    </p>

                    <div class="profile-actions">

                        <button class="call-btn">
                            ☎ Call Caretaker
                        </button>

                        <button class="message-btn">
                            ✉ Message Support
                        </button>

                    </div>

                </div>

            </div>


            <div class="profile-right">

                <div class="adherence-box">

                    <span>MEDICATION ADHERENCE</span>

                    <strong>94%</strong>

                    <div class="progress-bar">
                        <div class="progress-fill" style="width: 94%;"></div>
                    </div>

                    <small>Target: 95%</small>

                </div>


                <div class="last-update">

                    <span>LAST UPDATE</span>

                    <strong>Today, 9:45 AM</strong>

                </div>

            </div>

        </section>


        <!-- =========================
             HEALTH STATISTICS
        ========================== -->
        <section class="stats-grid">

            <div class="stat-card">

                <div class="stat-icon heart">
                    ♡
                </div>

                <div>
                    <span>AVG HEART RATE</span>
                    <strong>72 BPM</strong>
                </div>

                <small class="positive">↗ 2%</small>

            </div>


            <div class="stat-card">

                <div class="stat-icon sleep">
                    ◷
                </div>

                <div>
                    <span>SLEEP DURATION</span>
                    <strong>7h 45m</strong>
                </div>

                <small class="negative">↘ 12m</small>

            </div>


            <div class="stat-card">

                <div class="stat-icon weight">
                    ≈
                </div>

                <div>
                    <span>WEIGHT</span>
                    <strong>85 kg</strong>
                </div>

                <small class="positive">↗ 15%</small>

            </div>


            <div class="stat-card">

                <div class="stat-icon pain">
                    ♧
                </div>

                <div>
                    <span>AVG PAIN LEVEL</span>
                    <strong>2.1 / 10</strong>
                </div>

                <small class="negative">↘ 0.5</small>

            </div>

        </section>


        <!-- =========================
             TWO COLUMN CONTENT
        ========================== -->
        <section class="content-grid">


            <!-- =========================
                 LEFT COLUMN
            ========================== -->
            <div class="left-column">


                <!-- HEALTH TRENDS -->
                <div class="card health-trends">

                    <div class="card-header">

                        <div>
                            <h2>Health Trends</h2>
                            <p>Subjective metrics over the last 7 days</p>
                        </div>

                        <button class="view-button">
                            WEEKLY VIEW
                        </button>

                    </div>


                    <!-- Simple CSS chart -->
                    <div class="chart-container">

                        <div class="y-axis">
                            <span>12</span>
                            <span>10</span>
                            <span>8</span>
                            <span>6</span>
                            <span>4</span>
                            <span>2</span>
                        </div>

                        <div class="chart">

                            <div class="grid-line line-1"></div>
                            <div class="grid-line line-2"></div>
                            <div class="grid-line line-3"></div>
                            <div class="grid-line line-4"></div>

                            <svg viewBox="0 0 700 220"
                                 preserveAspectRatio="none">

                                <polyline
                                    points="0,130 100,155 200,115 300,165 400,130 500,100 600,115 700,125"
                                    class="blue-line"
                                />

                                <polyline
                                    points="0,145 100,155 200,120 300,170 400,135 500,105 600,110 700,125"
                                    class="green-line"
                                />

                                <polyline
                                    points="0,185 100,160 200,195 300,165 400,185 500,200 600,190 700,195"
                                    class="red-line"
                                />

                            </svg>

                        </div>

                    </div>


                    <div class="chart-days">
                        <span>Mon</span>
                        <span>Tue</span>
                        <span>Wed</span>
                        <span>Thu</span>
                        <span>Fri</span>
                        <span>Sat</span>
                        <span>Sun</span>
                    </div>

                </div>


                <!-- CAREGIVER VISITS -->
                <div class="card caregiver-card">

                    <div class="card-header">

                        <div>
                            <h2>Caregiver Visit Records</h2>
                            <p>Most recent observations and clinical notes</p>
                        </div>

                        <a href="#" class="history-link">
                            View History
                        </a>

                    </div>


                    <div class="visit">

                        <div class="visit-avatar">
                            U
                        </div>

                        <div class="visit-content">

                            <div class="visit-top">

                                <strong>User</strong>

                                <span>
                                    Today, 9:45 AM
                                </span>

                            </div>

                            <small>Caregiver</small>

                            <p>
                                "Patient was in high spirits.
                                Completed morning exercises and
                                finished breakfast."
                            </p>

                        </div>

                        <span class="visit-arrow">›</span>

                    </div>


                    <div class="visit">

                        <div class="visit-avatar muted">
                            U
                        </div>

                        <div class="visit-content">

                            <div class="visit-top">

                                <strong>User</strong>

                                <span>
                                    Yesterday, 2:00 PM
                                </span>

                            </div>

                            <small>Physical Therapist</small>

                            <p>
                                "Patient has lowered body mobility.
                                Progressing well with assisted walking."
                            </p>

                        </div>

                        <span class="visit-arrow">›</span>

                    </div>

                </div>

            </div>


            <!-- =========================
                 RIGHT COLUMN
            ========================== -->
            <div class="right-column">


                <!-- SYSTEM ALERTS -->
                <div class="card alerts-card">

                    <div class="alerts-header">

                        <h2>
                            ⓘ System Alerts
                        </h2>

                    </div>


                    <div class="alert-item">

                        <div class="alert-title">
                            <strong>MISSED LUNCH RECORD</strong>
                            <span>2 hours ago</span>
                        </div>

                        <p>
                            Caregiver reported minimal appetite
                            during the noon visit.
                        </p>

                        <div class="alert-actions">

                            <button class="dismiss-btn">
                                DISMISS
                            </button>

                            <button class="action-btn">
                                ACTION
                            </button>

                        </div>

                    </div>


                    <div class="alert-item">

                        <div class="alert-title">
                            <strong>LOW MOBILITY DETECTED</strong>
                            <span>5 hours ago</span>
                        </div>

                        <p>
                            Step count is 40% below daily average
                            for this time.
                        </p>

                        <div class="alert-actions">

                            <button class="dismiss-btn">
                                DISMISS
                            </button>

                            <button class="action-btn">
                                ACTION
                            </button>

                        </div>

                    </div>

                </div>


                <!-- MEDICATION -->
                <div class="card medication-card">

                    <div class="card-header">

                        <div>
                            <h2>Daily Medication</h2>
                            <p>Scheduled dosages for today</p>
                        </div>

                        <span>◷</span>

                    </div>


                    <div class="medication-item completed">

                        <div class="medication-icon">
                            ✓
                        </div>

                        <div class="medication-info">

                            <strong>10:00 AM</strong>

                            <span>10mg • Blood Pressure</span>

                        </div>

                        <span class="med-status taken">
                            TAKEN
                        </span>

                    </div>


                    <div class="medication-item">

                        <div class="medication-icon">
                            ◷
                        </div>

                        <div class="medication-info">

                            <strong>12:30 PM</strong>

                            <span>500mg • Diabetes</span>

                        </div>

                        <span class="med-status pending">
                            UPCOMING
                        </span>

                    </div>


                    <div class="medication-item">

                        <div class="medication-icon">
                            ◷
                        </div>

                        <div class="medication-info">

                            <strong>08:00 PM</strong>

                            <span>20mg • Cholesterol</span>

                        </div>

                        <span class="med-status pending">
                            PENDING
                        </span>

                    </div>


                    <button class="full-medication">
                        ⌕ Full Medication Schedule
                    </button>

                </div>


                <!-- DIETARY ADHERENCE -->
                <div class="card dietary-card">

                    <div class="diet-header">

                        <div>
                            <h2>Dietary Adherence</h2>
                            <p>Calories & hydration</p>
                        </div>

                    </div>


                    <div class="diet-item">

                        <div class="diet-label">
                            <span>Hydration</span>
                            <strong>1,400 / 2,000 ml</strong>
                        </div>

                        <div class="diet-progress">
                            <div style="width: 70%;"></div>
                        </div>

                    </div>


                    <div class="diet-item">

                        <div class="diet-label">
                            <span>Nutrition</span>
                            <strong>85% Intake</strong>
                        </div>

                        <div class="diet-progress">
                            <div style="width: 85%;"></div>
                        </div>

                    </div>

                </div>

            </div>

        </section>

    </main>

</div>

</body>
</html>