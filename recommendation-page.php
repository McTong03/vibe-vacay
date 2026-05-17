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
    <title>Vibe Vacay - Malaysia</title>
    <link rel="stylesheet" href="css/menubar.css">
    <link rel="stylesheet" href="css/recommendation-page.css">
</head>
<body>

    <?php include('./includes/navbar.php'); ?>

    <section class="hero">
        <button class="nav-arrow left">&#8592;</button>
        <div class="hero-content">
            <h1>Kuala Lumpur</h1>
            <p>Malaysia's capital city blends modern skyscrapers with cultural heritage. From iconic landmarks to shopping and food experiences, it's perfect for travelers seeking energy and excitement.</p>
            <div class="tags">
                <span class="tag-label">Tag:</span>
                <span class="tag">Urban</span>
                <span class="tag">Vibrant</span>
                <span class="tag">Lifestyle</span>
            </div>
        </div>
        <div class="rating-box">
            <span class="score">5.0</span>
            <span class="text">Rating</span>
        </div>
        <button class="nav-arrow right">&#8594;</button>
    </section>

    <div class="search-container">
        <div class="search-bar">
            <input type="text" placeholder="Find places and things to do">
            <button>Search</button>
        </div>
    </div>

    <div class="container">
        <section class="destinations-section">
            <h2 class="section-title">High Rate of Travel Destination In Malaysia</h2>
            
            <div class="cards-wrapper">
                <div class="dest-card">
                    <div class="heart-icon">&#9825;</div>
                    <img src="https://images.unsplash.com/photo-1518182170546-076616fdcbca?auto=format&fit=crop&q=80&w=400" alt="Blue Tears">
                    <div class="card-info">
                        <span class="location">Kuala Selangor</span>
                        <h3 class="dest-title">Magical Blue Tears Night Boat Tour in Kuala Selangor</h3>
                        <span class="climate">Climate: Summer</span>
                        <div class="card-footer">
                            <div class="rating">
                                <span class="score">5.0</span>
                                <span class="star">&#9733;</span>
                                <span class="reviews">(1,148)</span>
                            </div>
                            <div class="price">From RM 31</div>
                        </div>
                    </div>
                </div>

                <div class="dest-card">
                    <div class="heart-icon">&#9825;</div>
                    <img src="https://images.unsplash.com/photo-1584286595398-a59f21d313f5?auto=format&fit=crop&q=80&w=400" alt="Petronas Towers">
                    <div class="card-info">
                        <span class="location">Kuala Lumpur</span>
                        <h3 class="dest-title">Skip-the-Line Petronas Twin Towers E-Ticket</h3>
                        <span class="climate">Climate: Summer</span>
                        <div class="card-footer">
                            <div class="rating">
                                <span class="score">4.6</span>
                                <span class="star">&#9733;</span>
                                <span class="reviews">(3,148)</span>
                            </div>
                            <div class="price">From RM182</div>
                        </div>
                    </div>
                </div>

                <div class="dest-card">
                    <div class="heart-icon">&#9825;</div>
                    <img src="https://images.unsplash.com/photo-1528181304800-259b08848526?auto=format&fit=crop&q=80&w=400" alt="Sky Mirror">
                    <div class="card-info">
                        <span class="location">Kuala Selangor</span>
                        <h3 class="dest-title">Magical Sky Mirror Experience in Kuala Selangor</h3>
                        <span class="climate">Climate: Summer</span>
                        <div class="card-footer">
                            <div class="rating">
                                <span class="score">4.6</span>
                                <span class="star">&#9733;</span>
                                <span class="reviews">(3,148)</span>
                            </div>
                            <div class="price">From RM182</div>
                        </div>
                    </div>
                </div>

                <div class="dest-card">
                    <div class="heart-icon">&#9825;</div>
                    <img src="https://images.unsplash.com/photo-1550184658-ff6132a71714?auto=format&fit=crop&q=80&w=400" alt="Rafflesia Flower">
                    <div class="card-info">
                        <span class="location">Kuala Lumpur · Lojing Highlands</span>
                        <h3 class="dest-title">Rafflesia Flower Trek at Lojing Highlands</h3>
                        <span class="climate">Climate: Mountain</span>
                        <div class="card-footer">
                            <div class="rating">
                                <span class="score">4.6</span>
                                <span class="star">&#9733;</span>
                                <span class="reviews">(3,148)</span>
                            </div>
                            <div class="price">From RM182</div>
                        </div>
                    </div>
                </div>
            </div>
            
            <button class="slider-arrow">&#8594;</button>
        </section>

        <section class="states-section">
            <h2 class="section-title">State In Malaysia</h2>
            
            <div class="states-grid">
                <div class="state-card">
                    <img src="https://images.unsplash.com/photo-1596422846543-75c6fc197f07?auto=format&fit=crop&q=80&w=400" alt="Kuala Lumpur">
                    <h3>Kuala Lumpur</h3>
                </div>
                <div class="state-card">
                    <img src="https://images.unsplash.com/photo-1610884631558-86fc35bc61eb?auto=format&fit=crop&q=80&w=400" alt="Melaka">
                    <h3>Melaka</h3>
                </div>
                <div class="state-card">
                    <img src="https://images.unsplash.com/photo-1582801642861-12001e74f1ec?auto=format&fit=crop&q=80&w=400" alt="Negeri Sembilan">
                    <h3>Negeri Sembilan</h3>
                </div>
                <div class="state-card">
                    <img src="https://images.unsplash.com/photo-1621868357777-6a2c206cfd15?auto=format&fit=crop&q=80&w=400" alt="Johor Bahru">
                    <h3>Johor Bahru</h3>
                </div>
                
                <div class="state-card">
                    <img src="https://images.unsplash.com/photo-1587823528410-b96057bbfa74?auto=format&fit=crop&q=80&w=400" alt="Kedah">
                    <h3>Kedah</h3>
                </div>
                <div class="state-card">
                    <img src="https://images.unsplash.com/photo-1563200938-230af97f1fbc?auto=format&fit=crop&q=80&w=400" alt="Kelantan">
                    <h3>Kelantan</h3>
                </div>
                <div class="state-card">
                    <img src="https://images.unsplash.com/photo-1590050752117-238cb0fb12b1?auto=format&fit=crop&q=80&w=400" alt="Pahang">
                    <h3>Pahang</h3>
                </div>
                <div class="state-card">
                    <img src="https://images.unsplash.com/photo-1605333555546-f9479b4890d2?auto=format&fit=crop&q=80&w=400" alt="Perak">
                    <h3>Perak</h3>
                </div>

                <div class="state-card">
                    <img src="https://images.unsplash.com/photo-1517436073-3b1b11ce1163?auto=format&fit=crop&q=80&w=400" alt="Perlis">
                    <h3>Perlis</h3>
                </div>
                <div class="state-card">
                    <img src="https://images.unsplash.com/photo-1580219616035-188ccbc05b22?auto=format&fit=crop&q=80&w=400" alt="Pulau Pinang">
                    <h3>Pulau Pinang</h3>
                </div>
                <div class="state-card">
                    <img src="https://images.unsplash.com/photo-1588693959253-8d655f4581f3?auto=format&fit=crop&q=80&w=400" alt="Selangor">
                    <h3>Selangor</h3>
                </div>
                <div class="state-card">
                    <img src="https://images.unsplash.com/photo-1628174780517-5eeb8e94589c?auto=format&fit=crop&q=80&w=400" alt="Terengganu">
                    <h3>Terengganu</h3>
                </div>
            </div>
        </section>

        ``<h2 class="section-title">What people are saying about Their Tour</h2>

        <div class="reviews-grid">
            
            <div class="review-card">
                <a href="#" class="tour-link">Magical Blue Tears Night Boat Tour in Kuala Selangor</a>
                <div class="stars">★★★★★</div>
                
                <div class="user-info">
                    <div class="avatar bg-orange">B</div>
                    <div class="user-details">
                        <div class="name">Briege</div>
                        <div class="date">March 31, 2026</div>
                    </div>
                </div>
                
                <p class="review-text">Moinya I hope I spelt this right was an amazing guide so knowledgeable fun and passionate about the tour we had the best time and were completely blew away with this tour would definitely...</p>
                <a class="show-more">Show more</a>
                
                <div class="review-images">
                    <div class="img-wrapper"><img src="https://images.unsplash.com/photo-1548625361-ec853c896944?auto=format&fit=crop&w=150&q=80" alt="Tour image"></div>
                    <div class="img-wrapper"><img src="https://images.unsplash.com/photo-1518182170546-076616fdcbca?auto=format&fit=crop&w=150&q=80" alt="Tour image"></div>
                    <div class="img-wrapper">
                        <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=150&q=80" alt="Tour image">
                        <div class="img-overlay">+1</div>
                    </div>
                </div>
            </div>

            <div class="review-card">
                <a href="#" class="tour-link">NYC: Metropolitan Museum: "Secrets of the MET" Experience</a>
                <div class="stars">★★★★★</div>
                
                <div class="user-info">
                    <div class="avatar bg-orange">P</div>
                    <div class="user-details">
                        <div class="name">Pascal</div>
                        <div class="date">March 29, 2026</div>
                    </div>
                </div>
                
                <p class="review-text">Great! Very informative, lovely friendly driver, mountains of food! All very good.</p>
                
                <div class="review-images" style="margin-top: auto;">
                    <div class="img-wrapper"><img src="https://images.unsplash.com/photo-1565121544322-835616b49e25?auto=format&fit=crop&w=150&q=80" alt="Tour image"></div>
                </div>
            </div>

            <div class="review-card">
                <a href="#" class="tour-link">Magical Blue Tears Night Boat Tour in Kuala Selangor</a>
                <div class="stars">★★★★★</div>
                
                <div class="user-info">
                    <div class="avatar bg-red">E</div>
                    <div class="user-details">
                        <div class="name">Elmar</div>
                        <div class="date">March 31, 2026</div>
                    </div>
                </div>
                
                <p class="review-text">An amazing experience! Our guide was highly experienced, incredibly passionate, and made the entire tour truly engaging. You could immediately tell how much he loves his job. The tour was...</p>
                <a class="show-more">Show more</a>
                
                <div class="review-images">
                    <div class="img-wrapper"><img src="https://images.unsplash.com/photo-1542317148-8badecc4eb97?auto=format&fit=crop&w=150&q=80" alt="Tour image"></div>
                    <div class="img-wrapper"><img src="https://images.unsplash.com/photo-1604928141064-207cea6f571f?auto=format&fit=crop&w=150&q=80" alt="Tour image"></div>
                    <div class="img-wrapper">
                        <img src="https://images.unsplash.com/photo-1518182170546-076616fdcbca?auto=format&fit=crop&w=150&q=80" alt="Tour image">
                        <div class="img-overlay">+1</div>
                    </div>
                </div>
            </div>

            <div class="review-card">
                <a href="#" class="tour-link">NYC: Metropolitan Museum: "Secrets of the MET" Experience</a>
                <div class="stars">★★★★★</div>
                
                <div class="user-info">
                    <div class="avatar bg-orange">P</div>
                    <div class="user-details">
                        <div class="name">Pascal</div>
                        <div class="date">March 29, 2026</div>
                    </div>
                </div>
                
                <p class="review-text">Great! Very informative, lovely friendly driver, mountains of food! All very good.</p>
                
                <div class="review-images" style="margin-top: auto;">
                    <div class="img-wrapper"><img src="https://images.unsplash.com/photo-1565121544322-835616b49e25?auto=format&fit=crop&w=150&q=80" alt="Tour image"></div>
                </div>
            </div>

            <div class="review-card">
                <a href="#" class="tour-link">Magical Blue Tears Night Boat Tour in Kuala Selangor</a>
                <div class="stars">★★★★★</div>
                
                <div class="user-info">
                    <div class="avatar bg-red">E</div>
                    <div class="user-details">
                        <div class="name">Elmar</div>
                        <div class="date">March 31, 2026</div>
                    </div>
                </div>
                
                <p class="review-text">An amazing experience! Our guide was highly experienced, incredibly passionate, and made the entire tour truly engaging. You could immediately tell how much he loves his job. The tour was...</p>
                <a class="show-more">Show more</a>
                
                <div class="review-images">
                    <div class="img-wrapper"><img src="https://images.unsplash.com/photo-1542317148-8badecc4eb97?auto=format&fit=crop&w=150&q=80" alt="Tour image"></div>
                    <div class="img-wrapper"><img src="https://images.unsplash.com/photo-1604928141064-207cea6f571f?auto=format&fit=crop&w=150&q=80" alt="Tour image"></div>
                    <div class="img-wrapper">
                        <img src="https://images.unsplash.com/photo-1518182170546-076616fdcbca?auto=format&fit=crop&w=150&q=80" alt="Tour image">
                        <div class="img-overlay">+1</div>
                    </div>
                </div>
            </div>

            <div class="review-card">
                <a href="#" class="tour-link">Magical Blue Tears Night Boat Tour in Kuala Selangor</a>
                <div class="stars">★★★★★</div>
                
                <div class="user-info">
                    <div class="avatar bg-orange">B</div>
                    <div class="user-details">
                        <div class="name">Briege</div>
                        <div class="date">March 31, 2026</div>
                    </div>
                </div>
                
                <p class="review-text">Moinya I hope I spelt this right was an amazing guide so knowledgeable fun and passionate about the tour we had the best time and were completely blew away with this tour would definitely...</p>
                <a class="show-more">Show more</a>
                
                <div class="review-images">
                    <div class="img-wrapper"><img src="https://images.unsplash.com/photo-1548625361-ec853c896944?auto=format&fit=crop&w=150&q=80" alt="Tour image"></div>
                    <div class="img-wrapper"><img src="https://images.unsplash.com/photo-1518182170546-076616fdcbca?auto=format&fit=crop&w=150&q=80" alt="Tour image"></div>
                    <div class="img-wrapper">
                        <img src="https://images.unsplash.com/photo-1507525428034-b723cf961d3e?auto=format&fit=crop&w=150&q=80" alt="Tour image">
                        <div class="img-overlay">+1</div>
                    </div>
                </div>
            </div>

        </div>

        <div class="view-more-container">
            <button class="btn-view-more">View More</button>
        </div>
        
    </div>

</body>
</html>