<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
include 'conn.php';
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>State Destination Page</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/menubar.css">
    <link rel="stylesheet" href="css/state-destination.css">
</head>


<body>
    <?php include('./includes/navbar.php'); ?>

    <!-- ── STATE / HERO ── -->
    <section class="state">
    <div class="state-content">

        <!-- Back button + Title side by side -->
        <div class="state-title-row">
            <button type="button" class="back_Btn" onclick="window.location.href='tagging-type-management.php'">
            <img src="icon/error.png" class="back-icon" />
        </button>
            <h1>Kuala Lumpur</h1>
        </div>

        <p class="state-desc">
            Malaysia's capital city blends modern skyscrapers with cultural heritage. From iconic landmarks to
            shopping districts and street food — KL is endlessly vibrant, buzzing with energy and excitement.
        </p>
        <div class="state-meta">
            <div class="state-tags">
                <span class="state-tag">Urban</span>
                <span class="state-tag">Vibrant</span>
                <span class="state-tag">Lifestyle</span>
            </div>
        </div>
    </div>
</section>

    <!-- ── SEARCH ── -->
    <div class="search-container">
        <div class="search-bar">
            <input type="text" placeholder="Find places and things to do">
            <button>Search</button>
        </div>
    </div>

    <!-- ── Top Sights ── -->
    <div class="destination-list">
        <div class="section-title-row">
            <h1>Top Sights In Kuala Lumpur</h1>
        </div>
        <div class="destination-cards">

            <div class="dest-card" onclick="window.location.href='destination-description.php'">
                <div class="heart-icon">&#9825;</div>
                <img class="thumbnail"
                    src="https://images.unsplash.com/photo-1540306354890-50b9ebef1e1c?auto=format&fit=crop&q=80&w=400"
                    alt="KL Tower">
                <div class="card-info">
                    <span class="location">Kuala Lumpur</span>
                    <h3 class="dest-title">Menara Kuala Lumpur Observation Deck Ticket</h3>
                    <span class="climate">Climate: Summer</span>
                    <div class="card-footer">
                        <div class="rating"><span class="score">4.7</span><span class="star">&#9733;</span><span
                                class="reviews">(1,748)</span></div>
                        <div class="price">From RM140</div>
                    </div>
                </div>
            </div>

            <div class="dest-card" onclick="window.location.href='destination-description.php'">
                <div class="heart-icon">&#9825;</div>
                <img class="thumbnail"
                    src="https://images.unsplash.com/photo-1596422846543-75c6fc197f11?auto=format&fit=crop&q=80&w=400"
                    alt="Petronas">
                <div class="card-info">
                    <span class="location">Kuala Lumpur</span>
                    <h3 class="dest-title">Skip-the-Line Petronas Twin Towers E-Ticket</h3>
                    <span class="climate">Climate: Summer</span>
                    <div class="card-footer">
                        <div class="rating"><span class="score">4.9</span><span class="star">&#9733;</span><span
                                class="reviews">(5,210)</span></div>
                        <div class="price">From RM182</div>
                    </div>
                </div>
            </div>

            <div class="dest-card" onclick="window.location.href='destination-description.php'">
                <div class="heart-icon">&#9825;</div>
                <img class="thumbnail"
                    src="https://images.unsplash.com/photo-1555217851-6141535bd771?auto=format&fit=crop&q=80&w=400"
                    alt="Batu Caves">
                <div class="card-info">
                    <span class="location">Kuala Lumpur</span>
                    <h3 class="dest-title">Batu Caves Guided Heritage Tour</h3>
                    <span class="climate">Climate: Summer</span>
                    <div class="card-footer">
                        <div class="rating"><span class="score">4.6</span><span class="star">&#9733;</span><span
                                class="reviews">(3,148)</span></div>
                        <div class="price">From RM55</div>
                    </div>
                </div>
            </div>

            <div class="dest-card" onclick="window.location.href='destination-description.php'">
                <div class="heart-icon">&#9825;</div>
                <img class="thumbnail"
                    src="https://images.unsplash.com/photo-1508009603885-50cf7c579365?auto=format&fit=crop&q=80&w=400"
                    alt="Aquaria">
                <div class="card-info">
                    <span class="location">Kuala Lumpur</span>
                    <h3 class="dest-title">Aquaria KLCC Entry Ticket</h3>
                    <span class="climate">Climate: Summer</span>
                    <div class="card-footer">
                        <div class="rating"><span class="score">4.5</span><span class="star">&#9733;</span><span
                                class="reviews">(2,901)</span></div>
                        <div class="price">From RM65</div>
                    </div>
                </div>
            </div>
            <button type="button" class="view-more-circle" onclick="window.location.href='tagging-type-management.php'">
                <img src="icon/error.png" class="view-icon" />
            </button>
        </div>
    </div>

    <!-- ── Outdoor Attractions ── -->
    <div class="destination-list">
        <div class="section-title-row">
            <h1>Outdoor Attractions In Kuala Lumpur</h1>
        </div>
        <div class="destination-cards">

            <div class="dest-card" onclick="window.location.href='destination-description.php'">
                <div class="heart-icon">&#9825;</div>
                <img class="thumbnail"
                    src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&q=80&w=400"
                    alt="KLCC Park">
                <div class="card-info">
                    <span class="location">Kuala Lumpur</span>
                    <h3 class="dest-title">KLCC Park &amp; Fountain Show</h3>
                    <span class="climate">Climate: Summer</span>
                    <div class="card-footer">
                        <div class="rating"><span class="score">4.6</span><span class="star">&#9733;</span><span
                                class="reviews">(3,102)</span></div>
                        <div class="price">Free</div>
                    </div>
                </div>
            </div>

            <div class="dest-card" onclick="window.location.href='destination-description.php'">
                <div class="heart-icon">&#9825;</div>
                <img class="thumbnail"
                    src="https://images.unsplash.com/photo-1441974231531-c6227db76b6e?auto=format&fit=crop&q=80&w=400"
                    alt="Bukit Nanas">
                <div class="card-info">
                    <span class="location">Kuala Lumpur</span>
                    <h3 class="dest-title">Bukit Nanas Forest Reserve Trail</h3>
                    <span class="climate">Climate: Summer</span>
                    <div class="card-footer">
                        <div class="rating"><span class="score">4.4</span><span class="star">&#9733;</span><span
                                class="reviews">(892)</span></div>
                        <div class="price">Free</div>
                    </div>
                </div>
            </div>

            <div class="dest-card" onclick="window.location.href='destination-description.php'">
                <div class="heart-icon">&#9825;</div>
                <img class="thumbnail"
                    src="https://images.unsplash.com/photo-1518791841217-8f162f1912da?auto=format&fit=crop&q=80&w=400"
                    alt="KL Bird Park">
                <div class="card-info">
                    <span class="location">Kuala Lumpur</span>
                    <h3 class="dest-title">KL Bird Park Entry Ticket</h3>
                    <span class="climate">Climate: Summer</span>
                    <div class="card-footer">
                        <div class="rating"><span class="score">4.5</span><span class="star">&#9733;</span><span
                                class="reviews">(4,430)</span></div>
                        <div class="price">From RM67</div>
                    </div>
                </div>
            </div>

            <div class="dest-card" onclick="window.location.href='destination-description.php'">
                <div class="heart-icon">&#9825;</div>
                <img class="thumbnail"
                    src="https://images.unsplash.com/photo-1501854140801-50d01698950b?auto=format&fit=crop&q=80&w=400"
                    alt="Perdana Botanical">
                <div class="card-info">
                    <span class="location">Kuala Lumpur</span>
                    <h3 class="dest-title">Perdana Botanical Garden Guided Walk</h3>
                    <span class="climate">Climate: Summer</span>
                    <div class="card-footer">
                        <div class="rating"><span class="score">4.3</span><span class="star">&#9733;</span><span
                                class="reviews">(1,205)</span></div>
                        <div class="price">Free</div>
                    </div>
                </div>
            </div>
            <button type="button" class="view-more-circle" onclick="window.location.href='tagging-type-management.php'">
                <img src="icon/error.png" class="view-icon" />
            </button>
        </div>
    </div>

    <!-- ── Historical Places ── -->
    <div class="destination-list">
        <div class="section-title-row">
            <h1>Historical Places In Kuala Lumpur</h1>
        </div>
        <div class="destination-cards">

            <div class="dest-card" onclick="window.location.href='destination-description.php'">
                <div class="heart-icon">&#9825;</div>
                <img class="thumbnail"
                    src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?auto=format&fit=crop&q=80&w=400"
                    alt="Sultan Abdul Samad">
                <div class="card-info">
                    <span class="location">Kuala Lumpur</span>
                    <h3 class="dest-title">Sultan Abdul Samad Building Walking Tour</h3>
                    <span class="climate">Climate: Summer</span>
                    <div class="card-footer">
                        <div class="rating"><span class="score">4.5</span><span class="star">&#9733;</span><span
                                class="reviews">(2,340)</span></div>
                        <div class="price">Free</div>
                    </div>
                </div>
            </div>

            <div class="dest-card" onclick="window.location.href='destination-description.php'">
                <div class="heart-icon">&#9825;</div>
                <img class="thumbnail"
                    src="https://images.unsplash.com/photo-1568454537842-d933259bb258?auto=format&fit=crop&q=80&w=400"
                    alt="National Museum">
                <div class="card-info">
                    <span class="location">Kuala Lumpur</span>
                    <h3 class="dest-title">National Museum KL Admission Ticket</h3>
                    <span class="climate">Climate: Summer</span>
                    <div class="card-footer">
                        <div class="rating"><span class="score">4.3</span><span class="star">&#9733;</span><span
                                class="reviews">(1,560)</span></div>
                        <div class="price">From RM5</div>
                    </div>
                </div>
            </div>

            <div class="dest-card" onclick="window.location.href='destination-description.php'">
                <div class="heart-icon">&#9825;</div>
                <img class="thumbnail"
                    src="https://images.unsplash.com/photo-1587474260584-136574528ed5?auto=format&fit=crop&q=80&w=400"
                    alt="Merdeka Square">
                <div class="card-info">
                    <span class="location">Kuala Lumpur</span>
                    <h3 class="dest-title">Merdeka Square Heritage Walking Tour</h3>
                    <span class="climate">Climate: Summer</span>
                    <div class="card-footer">
                        <div class="rating"><span class="score">4.4</span><span class="star">&#9733;</span><span
                                class="reviews">(987)</span></div>
                        <div class="price">Free</div>
                    </div>
                </div>
            </div>

            <div class="dest-card" onclick="window.location.href='destination-description.php'">
                <div class="heart-icon">&#9825;</div>
                <img class="thumbnail"
                    src="https://images.unsplash.com/photo-1533929736458-ca588d08c8be?auto=format&fit=crop&q=80&w=400"
                    alt="Jamek Mosque">
                <div class="card-info">
                    <span class="location">Kuala Lumpur</span>
                    <h3 class="dest-title">Masjid Jamek Guided Cultural Tour</h3>
                    <span class="climate">Climate: Summer</span>
                    <div class="card-footer">
                        <div class="rating"><span class="score">4.6</span><span class="star">&#9733;</span><span
                                class="reviews">(2,118)</span></div>
                        <div class="price">Free</div>
                    </div>
                </div>
            </div>
            <button type="button" class="view-more-circle" onclick="window.location.href='tagging-type-management.php'">
                <img src="icon/error.png" class="view-icon" />
            </button>
        </div>
    </div>

    <!-- ── REVIEWS SECTION ── -->
    <div class="reviews-section">
        <h1>What people are saying about Kuala Lumpur</h1>
        <div class="review-cards" id="reviewGrid">

            <!-- Review 1 -->
            <div class="review-card">
                <div class="review-place">
                    <img class="review-place-img"
                        src="https://images.unsplash.com/photo-1540306354890-50b9ebef1e1c?auto=format&fit=crop&q=80&w=100"
                        alt="KL Tower">
                    <div>
                        <div class="review-place-name">Menara Kuala Lumpur</div>
                        <div class="review-place-rating"><span class="stars">★★★★★</span><span>5.0 · Excellent</span>
                        </div>
                    </div>
                </div>
                <div class="review-user">
                    <img class="review-avatar" src="https://i.pravatar.cc/60?img=1" alt="User">
                    <div>
                        <div class="review-username">Sarah Lim</div>
                        <div class="review-date">Reviewed 3 days ago</div>
                    </div>
                </div>
                <p class="review-comment">Absolutely stunning views from the top! The observation deck gives you a 360°
                    panorama of the whole city. Went at sunset and it was magical. Highly recommend buying tickets in
                    advance to skip the queue.</p>
                <div class="review-photos">
                    <img class="review-photo"
                        src="https://images.unsplash.com/photo-1540306354890-50b9ebef1e1c?auto=format&fit=crop&q=80&w=100"
                        alt="">
                    <img class="review-photo"
                        src="https://images.unsplash.com/photo-1596422846543-75c6fc197f11?auto=format&fit=crop&q=80&w=100"
                        alt="">
                    <img class="review-photo"
                        src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&q=80&w=100"
                        alt="">
                </div>
            </div>

            <!-- Review 2 -->
            <div class="review-card">
                <div class="review-place">
                    <img class="review-place-img"
                        src="https://images.unsplash.com/photo-1596422846543-75c6fc197f11?auto=format&fit=crop&q=80&w=100"
                        alt="Petronas">
                    <div>
                        <div class="review-place-name">Petronas Twin Towers</div>
                        <div class="review-place-rating"><span class="stars">★★★★★</span><span>4.9 · Outstanding</span>
                        </div>
                    </div>
                </div>
                <div class="review-user">
                    <img class="review-avatar" src="https://i.pravatar.cc/60?img=5" alt="User">
                    <div>
                        <div class="review-username">Ahmad Razif</div>
                        <div class="review-date">Reviewed 1 week ago</div>
                    </div>
                </div>
                <p class="review-comment">One of the most iconic buildings in the world and it did not disappoint. The
                    sky bridge is a highlight. Staff were friendly and the whole experience was well organised. Worth
                    every ringgit.</p>
                <div class="review-photos">
                    <img class="review-photo"
                        src="https://images.unsplash.com/photo-1596422846543-75c6fc197f11?auto=format&fit=crop&q=80&w=100"
                        alt="">
                    <img class="review-photo"
                        src="https://images.unsplash.com/photo-1555217851-6141535bd771?auto=format&fit=crop&q=80&w=100"
                        alt="">
                </div>
            </div>

            <!-- Review 3 -->
            <div class="review-card">
                <div class="review-place">
                    <img class="review-place-img"
                        src="https://images.unsplash.com/photo-1555217851-6141535bd771?auto=format&fit=crop&q=80&w=100"
                        alt="Batu Caves">
                    <div>
                        <div class="review-place-name">Batu Caves</div>
                        <div class="review-place-rating"><span class="stars">★★★★☆</span><span>4.6 · Great</span></div>
                    </div>
                </div>
                <div class="review-user">
                    <img class="review-avatar" src="https://i.pravatar.cc/60?img=9" alt="User">
                    <div>
                        <div class="review-username">Priya Nair</div>
                        <div class="review-date">Reviewed 2 weeks ago</div>
                    </div>
                </div>
                <p class="review-comment">An incredible Hindu temple nestled inside a limestone hill. The giant golden
                    Murugan statue at the entrance is awe-inspiring. Go early in the morning to avoid crowds and the
                    midday heat. 272 steps but totally worth it!</p>
                <div class="review-photos">
                    <img class="review-photo"
                        src="https://images.unsplash.com/photo-1555217851-6141535bd771?auto=format&fit=crop&q=80&w=100"
                        alt="">
                    <img class="review-photo"
                        src="https://images.unsplash.com/photo-1518791841217-8f162f1912da?auto=format&fit=crop&q=80&w=100"
                        alt="">
                    <img class="review-photo"
                        src="https://images.unsplash.com/photo-1508009603885-50cf7c579365?auto=format&fit=crop&q=80&w=100"
                        alt="">
                </div>
            </div>

            <!-- Hidden reviews -->
            <div class="review-card hidden">
                <div class="review-place">
                    <img class="review-place-img"
                        src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&q=80&w=100"
                        alt="KLCC Park">
                    <div>
                        <div class="review-place-name">KLCC Park</div>
                        <div class="review-place-rating"><span class="stars">★★★★★</span><span>4.7 · Excellent</span>
                        </div>
                    </div>
                </div>
                <div class="review-user">
                    <img class="review-avatar" src="https://i.pravatar.cc/60?img=12" alt="User">
                    <div>
                        <div class="review-username">Jason Tan</div>
                        <div class="review-date">Reviewed 3 weeks ago</div>
                    </div>
                </div>
                <p class="review-comment">Perfect place for a morning jog with the Twin Towers as your backdrop. The
                    fountains at night are stunning. Great for families too — kids loved the playground area and the
                    splash pool.</p>
                <div class="review-photos">
                    <img class="review-photo"
                        src="https://images.unsplash.com/photo-1506905925346-21bda4d32df4?auto=format&fit=crop&q=80&w=100"
                        alt="">
                    <img class="review-photo"
                        src="https://images.unsplash.com/photo-1470770903676-69b98201ea1c?auto=format&fit=crop&q=80&w=100"
                        alt="">
                </div>
            </div>

            <div class="review-card hidden">
                <div class="review-place">
                    <img class="review-place-img"
                        src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?auto=format&fit=crop&q=80&w=100"
                        alt="Sultan Abdul Samad">
                    <div>
                        <div class="review-place-name">Sultan Abdul Samad Building</div>
                        <div class="review-place-rating"><span class="stars">★★★★☆</span><span>4.5 · Very Good</span>
                        </div>
                    </div>
                </div>
                <div class="review-user">
                    <img class="review-avatar" src="https://i.pravatar.cc/60?img=20" alt="User">
                    <div>
                        <div class="review-username">Wei Ling Chong</div>
                        <div class="review-date">Reviewed 1 month ago</div>
                    </div>
                </div>
                <p class="review-comment">A Moorish-style architectural gem right in the heart of KL. Best viewed in the
                    evening when it's all lit up. The surrounding Merdeka Square area is also great for a leisurely
                    stroll through history.</p>
                <div class="review-photos">
                    <img class="review-photo"
                        src="https://images.unsplash.com/photo-1558618666-fcd25c85cd64?auto=format&fit=crop&q=80&w=100"
                        alt="">
                    <img class="review-photo"
                        src="https://images.unsplash.com/photo-1587474260584-136574528ed5?auto=format&fit=crop&q=80&w=100"
                        alt="">
                </div>
            </div>

            <div class="review-card hidden">
                <div class="review-place">
                    <img class="review-place-img"
                        src="https://images.unsplash.com/photo-1518791841217-8f162f1912da?auto=format&fit=crop&q=80&w=100"
                        alt="KL Bird Park">
                    <div>
                        <div class="review-place-name">KL Bird Park</div>
                        <div class="review-place-rating"><span class="stars">★★★★★</span><span>4.8 · Outstanding</span>
                        </div>
                    </div>
                </div>
                <div class="review-user">
                    <img class="review-avatar" src="https://i.pravatar.cc/60?img=33" alt="User">
                    <div>
                        <div class="review-username">Marcus Lee</div>
                        <div class="review-date">Reviewed 1 month ago</div>
                    </div>
                </div>
                <p class="review-comment">World's largest free-flight bird park — you walk among thousands of birds in a
                    huge netted enclosure. The hornbills and parrots are particularly impressive. Great for photography
                    enthusiasts!</p>
                <div class="review-photos">
                    <img class="review-photo"
                        src="https://images.unsplash.com/photo-1518791841217-8f162f1912da?auto=format&fit=crop&q=80&w=100"
                        alt="">
                    <img class="review-photo"
                        src="https://images.unsplash.com/photo-1501854140801-50d01698950b?auto=format&fit=crop&q=80&w=100"
                        alt="">
                </div>
            </div>

        </div>

        <div class="view-more-wrap">
            <button class="view-more-btn" id="viewMoreBtn" onclick="showMoreReviews()">View More</button>
        </div>
    </div>

    <script>
        function showMoreReviews() {
            document.querySelectorAll('.review-card.hidden').forEach(c => c.classList.remove('hidden'));
            document.getElementById('viewMoreBtn').style.display = 'none';
        }
    </script>

</body>

</html>