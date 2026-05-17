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
    <link rel="stylesheet" href="css/filter-search-page.css">
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