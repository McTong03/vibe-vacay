<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Vibe Vacay - Discover & Plan</title>
    <link rel="stylesheet" href="css/menubar.css">
    <style>
        /* Base Variables & Reset */
        :root {
            --primary-dark: #1e293b;
            --primary-blue: #0ea5e9;
            --text-main: #111827;
            --text-muted: #6b7280;
            --bg-light: #f9fafb;
            --border-color: #e5e7eb;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
        }

        body {
            color: var(--text-main);
            background-color: #ffffff;
            padding-bottom: 4rem;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 2rem;
        }

        /* Hero section adjusted for new navbar */
        .hero {
            padding-top: 2rem;
        }
        /* Hero & Filter Section */
        .hero {
            padding-top: 4rem;
            padding-bottom: 4rem;
            background: linear-gradient(rgba(0, 0, 0, 0.4), rgba(0, 0, 0, 0.4)),
                url('https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&q=80&w=1920') center/cover;
            display: flex;
            flex-direction: column;
            align-items: center;
            color: white;
            position: relative;
        }

        .hero h1 {
            font-size: 2.5rem;
            margin-bottom: 2rem;
            text-align: center;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.3);
        }

        .filter-panels-container {
            display: flex;
            gap: 1.5rem;
            width: 100%;
            max-width: 1100px;
        }

        /* Main Filter Box */
        .filter-box {
            background-color: #2b3a55;
            border-radius: 16px;
            padding: 1.5rem;
            flex: 2.5;
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .filter-header {
            font-size: 0.9rem;
            margin-bottom: 1rem;
            font-weight: 600;
        }

        .filter-grid {
            display: flex;
            gap: 2rem;
            margin-bottom: 1.5rem;
        }

        .mood-section,
        .dropdown-section {
            flex: 1;
        }

        .mood-section h3 {
            font-size: 0.9rem;
            margin-bottom: 0.8rem;
            font-weight: 600;
        }

        .mood-buttons {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 0.8rem;
        }

        .mood-btn {
            background-color: white;
            color: #333;
            border: none;
            padding: 0.6rem 1rem;
            border-radius: 30px;
            font-size: 0.85rem;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            cursor: pointer;
            transition: all 0.2s;
        }

        .mood-btn.active {
            background-color: #bae6fd;
            border: 2px solid var(--primary-blue);
        }

        .dropdown-section {
            display: flex;
            flex-direction: column;
            gap: 0.8rem;
            padding-top: 1.8rem;
        }

        .dropdown-section select {
            width: 100%;
            padding: 0.7rem 1rem;
            border-radius: 8px;
            border: none;
            outline: none;
            font-weight: 600;
            color: #333;
            appearance: none;
            background: white url("data:image/svg+xml;charset=US-ASCII,%3Csvg%20xmlns%3D%22http%3A%2F%2Fwww.w3.org%2F2000%2Fsvg%22%20width%3D%22292.4%22%20height%3D%22292.4%22%3E%3Cpath%20fill%3D%22%23333%22%20d%3D%22M287%2069.4a17.6%2017.6%200%200%200-13-5.4H18.4c-5%200-9.3%201.8-12.9%205.4A17.6%2017.6%200%200%200%200%2082.2c0%205%201.8%209.3%205.4%2012.9l128%20127.9c3.6%203.6%207.8%205.4%2012.8%205.4s9.2-1.8%2012.8-5.4L287%2095c3.5-3.5%205.4-7.8%205.4-12.8%200-5-1.9-9.2-5.4-12.8z%22%2F%3E%3C%2Fsvg%3E") no-repeat right 1rem center;
            background-size: 12px;
        }

        .toggle-section {
            background: white;
            color: #333;
            padding: 0.8rem 1rem;
            border-radius: 8px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            font-weight: 600;
        }

        .toggle-section span.label {
            margin-bottom: 0.5rem;
            display: block;
            color: white;
        }

        .switch {
            position: relative;
            display: inline-block;
            width: 46px;
            height: 24px;
        }

        .switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0; left: 0; right: 0; bottom: 0;
            background-color: #ccc;
            transition: .4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked+.slider {
            background-color: var(--primary-blue);
        }

        input:checked+.slider:before {
            transform: translateX(22px);
        }

        .filter-actions {
            display: flex;
            justify-content: center;
            gap: 1rem;
        }

        .btn {
            padding: 0.6rem 2rem;
            border-radius: 30px;
            font-weight: bold;
            font-size: 0.9rem;
            cursor: pointer;
            border: none;
        }

        .btn-clear {
            background-color: white;
            color: var(--primary-dark);
        }

        .btn-search {
            background-color: var(--primary-blue);
            color: white;
        }

        /* Random Recommendation Box */
        .random-box {
            background-color: white;
            border-radius: 16px;
            padding: 1.5rem;
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            color: var(--text-main);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }

        .random-box h3 {
            font-size: 0.95rem;
            font-weight: bold;
            text-align: center;
        }

        .random-icon {
            width: 250px;
            height: 250px;
            margin: 1rem 0;
            object-fit: contain;
        }

        .btn-random {
            background-color: var(--primary-blue);
            color: white;
            width: 80%;
        }

        /* Global Search Bar */
        .search-container {
            display: flex;
            justify-content: center;
            margin-top: 2rem;
            padding-bottom: 2rem;
        }

        .search-bar {
            background-color: var(--primary-dark);
            padding: 0.5rem;
            padding-left: 1.5rem;
            border-radius: 40px;
            display: flex;
            width: 600px;
            justify-content: space-between;
            align-items: center;
        }

        .search-bar input {
            background: transparent;
            border: none;
            color: white;
            outline: none;
            width: 100%;
        }

        .search-bar input::placeholder {
            color: #cbd5e1;
        }

        .search-bar button {
            background-color: white;
            color: var(--primary-dark);
            border: none;
            padding: 0.6rem 2rem;
            border-radius: 30px;
            font-weight: bold;
            cursor: pointer;
        }

        /* Cards Grid */
        .cards-wrapper {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1.5rem;
            margin-top: 2rem;
        }

        .dest-card {
            border: 1px solid var(--border-color);
            border-radius: 12px;
            overflow: hidden;
            background: white;
            display: flex;
            flex-direction: column;
            position: relative;
        }

        .dest-card img.thumbnail {
            width: 100%;
            height: 160px;
            object-fit: cover;
        }

        .heart-icon {
            position: absolute;
            top: 10px;
            right: 10px;
            background: white;
            width: 28px;
            height: 28px;
            border-radius: 50%;
            display: flex;
            justify-content: center;
            align-items: center;
            font-size: 1.2rem;
            color: var(--text-muted);
            cursor: pointer;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .card-info {
            padding: 1rem;
            display: flex;
            flex-direction: column;
            flex-grow: 1;
        }

        .location {
            font-size: 0.75rem;
            color: var(--text-muted);
            margin-bottom: 0.25rem;
        }

        .dest-title {
            font-size: 0.95rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
            line-height: 1.3;
            min-height: 2.6em;
        }

        .climate {
            font-size: 0.8rem;
            color: var(--text-muted);
            margin-bottom: 1rem;
        }

        .card-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
            font-size: 0.85rem;
        }

        .rating {
            display: flex;
            align-items: center;
            gap: 0.2rem;
        }

        .star {
            color: #1e293b;
        }

        .reviews {
            color: var(--text-muted);
            font-size: 0.75rem;
        }

        .price {
            font-weight: bold;
        }
    </style>
</head>
<body>

    <?php include('./includes/navbar.php'); ?>

    <section class="hero">
        <h1>Discover & plan things to do</h1>
        
        <div class="filter-panels-container">
            <div class="filter-box">
                <div class="filter-header">Filter by:</div>
                
                <div class="filter-grid">
                    <div class="mood-section">
                        <h3>How do you feel today:</h3>
                        <div class="mood-buttons">
                            <button class="mood-btn active">😫 Stressed</button>
                            <button class="mood-btn">😐 Neutral</button>
                            <button class="mood-btn">😢 Sad</button>
                            <button class="mood-btn">😎 Adventurous</button>
                            <button class="mood-btn">😀 Happy</button>
                        </div>
                    </div>
                    
                    <div class="dropdown-section">
                        <select><option>Climate</option></select>
                        <select><option>Budget</option></select>
                        <select><option>Travel Companion</option></select>
                        <select><option>Destination Type</option></select>
                    </div>
                </div>

                <span class="label">Looking for Hidden Gems?</span>
                <div class="toggle-section">
                    <span>Hidden / Less Touristy</span>
                    <label class="switch">
                        <input type="checkbox" checked>
                        <span class="slider"></span>
                    </label>
                </div>

                <div class="filter-actions">
                    <button class="btn btn-clear">Clear</button>
                    <button class="btn btn-search">Search</button>
                </div>
            </div>

            <div class="random-box">
                <h3>Random Recommendation:</h3>
                <img src="Image/dice.png" alt="Dice Icon" class="random-icon">
                <button class="btn btn-random">Random</button>
            </div>
        </div>
    </section>

    <div class="container">
        <div class="search-container">
            <div class="search-bar">
                <input type="text" placeholder="Find places and things to do">
                <button>Search</button>
            </div>
        </div>

        <div class="cards-wrapper">
            <div class="dest-card">
                <div class="heart-icon">&#9825;</div>
                <img class="thumbnail" src="https://images.unsplash.com/photo-1544551763-46a013bb70d5?auto=format&fit=crop&q=80&w=400" alt="Scuba Diving">
                <div class="card-info">
                    <span class="location">Kuala Selangor</span>
                    <h3 class="dest-title">Magical Blue Tears Night Boat Tour in Kuala Selangor</h3>
                    <span class="climate">Climate: Summer</span>
                    <div class="card-footer">
                        <div class="rating">
                            <span class="score">5.0</span> <span class="star">&#9733;</span> <span class="reviews">(1,148)</span>
                        </div>
                        <div class="price">From RM 31</div>
                    </div>
                </div>
            </div>

            <div class="dest-card">
                <div class="heart-icon">&#9825;</div>
                <img class="thumbnail" src="https://images.unsplash.com/photo-1540306354890-50b9ebef1e1c?auto=format&fit=crop&q=80&w=400" alt="Batu Caves">
                <div class="card-info">
                    <span class="location">Kuala Lumpur</span>
                    <h3 class="dest-title">Skip-the-Line Petronas Twin Towers E-Ticket</h3>
                    <span class="climate">Climate: Summer</span>
                    <div class="card-footer">
                        <div class="rating">
                            <span class="score">4.6</span> <span class="star">&#9733;</span> <span class="reviews">(3,148)</span>
                        </div>
                        <div class="price">From RM182</div>
                    </div>
                </div>
            </div>

            <div class="dest-card">
                <div class="heart-icon">&#9825;</div>
                <img class="thumbnail" src="https://images.unsplash.com/photo-1506012787146-f92b2d7d6d96?auto=format&fit=crop&q=80&w=400" alt="Looking through binoculars">
                <div class="card-info">
                    <span class="location">Kuala Selangor</span>
                    <h3 class="dest-title">Magical Sky Mirror Experience in Kuala Selangor</h3>
                    <span class="climate">Climate: Summer</span>
                    <div class="card-footer">
                        <div class="rating">
                            <span class="score">4.6</span> <span class="star">&#9733;</span> <span class="reviews">(3,148)</span>
                        </div>
                        <div class="price">From RM182</div>
                    </div>
                </div>
            </div>

            <div class="dest-card">
                <div class="heart-icon">&#9825;</div>
                <img class="thumbnail" src="https://images.unsplash.com/photo-1601614050275-01e6ce4f5b5b?auto=format&fit=crop&q=80&w=400" alt="Temple">
                <div class="card-info">
                    <span class="location">Kuala Lumpur · Lojing Highlands</span>
                    <h3 class="dest-title">Rafflesia Flower Trek at Lojing Highlands</h3>
                    <span class="climate">Climate: Mountain</span>
                    <div class="card-footer">
                        <div class="rating">
                            <span class="score">4.6</span> <span class="star">&#9733;</span> <span class="reviews">(3,148)</span>
                        </div>
                        <div class="price">From RM182</div>
                    </div>
                </div>
            </div>

            <div class="dest-card">
                <div class="heart-icon">&#9825;</div>
                <img class="thumbnail" src="https://images.unsplash.com/photo-1518182170546-076616fdcbca?auto=format&fit=crop&q=80&w=400" alt="Fireflies/Blue tears">
                <div class="card-info">
                    <span class="location">Kuala Selangor</span>
                    <h3 class="dest-title">Magical Blue Tears Night Boat Tour in Kuala Selangor</h3>
                    <span class="climate">Climate: Summer</span>
                    <div class="card-footer">
                        <div class="rating">
                            <span class="score">5.0</span> <span class="star">&#9733;</span> <span class="reviews">(1,148)</span>
                        </div>
                        <div class="price">From RM 31</div>
                    </div>
                </div>
            </div>

            <div class="dest-card">
                <div class="heart-icon">&#9825;</div>
                <img class="thumbnail" src="https://images.unsplash.com/photo-1542317148-8badecc4eb97?auto=format&fit=crop&q=80&w=400" alt="Mangrove/Jungle">
                <div class="card-info">
                    <span class="location">Kuala Lumpur</span>
                    <h3 class="dest-title">Skip-the-Line Petronas Twin Towers E-Ticket</h3>
                    <span class="climate">Climate: Summer</span>
                    <div class="card-footer">
                        <div class="rating">
                            <span class="score">4.6</span> <span class="star">&#9733;</span> <span class="reviews">(3,148)</span>
                        </div>
                        <div class="price">From RM182</div>
                    </div>
                </div>
            </div>

            <div class="dest-card">
                <div class="heart-icon">&#9825;</div>
                <img class="thumbnail" src="https://images.unsplash.com/photo-1589136777351-fdc9c9cb166b?auto=format&fit=crop&q=80&w=400" alt="Mountain View">
                <div class="card-info">
                    <span class="location">Kuala Selangor</span>
                    <h3 class="dest-title">Magical Sky Mirror Experience in Kuala Selangor</h3>
                    <span class="climate">Climate: Summer</span>
                    <div class="card-footer">
                        <div class="rating">
                            <span class="score">4.6</span> <span class="star">&#9733;</span> <span class="reviews">(3,148)</span>
                        </div>
                        <div class="price">From RM182</div>
                    </div>
                </div>
            </div>

            <div class="dest-card">
                <div class="heart-icon">&#9825;</div>
                <img class="thumbnail" src="https://images.unsplash.com/photo-1628174780517-5eeb8e94589c?auto=format&fit=crop&q=80&w=400" alt="Art mural/Village">
                <div class="card-info">
                    <span class="location">Kuala Lumpur · Lojing Highlands</span>
                    <h3 class="dest-title">Rafflesia Flower Trek at Lojing Highlands</h3>
                    <span class="climate">Climate: Mountain</span>
                    <div class="card-footer">
                        <div class="rating">
                            <span class="score">4.6</span> <span class="star">&#9733;</span> <span class="reviews">(3,148)</span>
                        </div>
                        <div class="price">From RM182</div>
                    </div>
                </div>
            </div>

            <div class="dest-card">
                <div class="heart-icon">&#9825;</div>
                <img class="thumbnail" src="https://images.unsplash.com/photo-1596422846543-75c6fc197f07?auto=format&fit=crop&q=80&w=400" alt="Cave">
                <div class="card-info">
                    <span class="location">Kuala Selangor</span>
                    <h3 class="dest-title">Magical Blue Tears Night Boat Tour in Kuala Selangor</h3>
                    <span class="climate">Climate: Summer</span>
                    <div class="card-footer">
                        <div class="rating">
                            <span class="score">5.0</span> <span class="star">&#9733;</span> <span class="reviews">(1,148)</span>
                        </div>
                        <div class="price">From RM 31</div>
                    </div>
                </div>
            </div>

            <div class="dest-card">
                <div class="heart-icon">&#9825;</div>
                <img class="thumbnail" src="https://images.unsplash.com/photo-1517436073-3b1b11ce1163?auto=format&fit=crop&q=80&w=400" alt="Sunset viewing deck">
                <div class="card-info">
                    <span class="location">Kuala Lumpur</span>
                    <h3 class="dest-title">Skip-the-Line Petronas Twin Towers E-Ticket</h3>
                    <span class="climate">Climate: Summer</span>
                    <div class="card-footer">
                        <div class="rating">
                            <span class="score">4.6</span> <span class="star">&#9733;</span> <span class="reviews">(3,148)</span>
                        </div>
                        <div class="price">From RM182</div>
                    </div>
                </div>
            </div>
            
        </div>
    </div>

</body>
</html>