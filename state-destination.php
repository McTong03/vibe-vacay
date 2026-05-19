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
</head>
<style>
    :root {
        --primary-dark: #1e293b;
        --primary-blue: #0ea5e9;
        --navy: #1A2B49;
        --text-main: #111827;
        --text-muted: #6b7280;
        --border-color: #e5e7eb;
    }

    * {
        margin: 0;
        padding: 0;
        box-sizing: border-box;
        font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    }

    body {
        background-color: #f8fafc;
        color: var(--text-main);
        padding-bottom: 4rem;
    }

    /* Back button */
    .back_Btn {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: none;
        background-color: #1A2B49;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .back-icon {
        width: 22px;
        height: 22px;
        filter: brightness(0) invert(1);
    }

    /* ── STATE SECTION ── */
    .state {
        position: relative;
        height: 480px;
        background:
            linear-gradient(to bottom, rgba(10, 20, 40, 0.35) 0%, rgba(10, 20, 40, 0.68) 100%),
            url('https://images.unsplash.com/photo-1596422846543-75c6fc197f11?auto=format&fit=crop&q=80&w=1920') center/cover no-repeat;
        display: flex;
        flex-direction: column;
        justify-content: flex-end;
        padding: 0 3rem 2.5rem;
        margin-top: 20px;
    }

    /* Circular back button */
    .state-back {
        position: absolute;
        top: 1.5rem;
        left: 2rem;
        width: 42px;
        height: 42px;
        border-radius: 50%;
        background: rgba(255, 255, 255, 0.22);
        backdrop-filter: blur(8px);
        border: 1.5px solid rgba(255, 255, 255, 0.4);
        color: white;
        font-size: 1.2rem;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        text-decoration: none;
        transition: background 0.2s;
        /* override any default button styles */
        outline: none;
        box-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
    }

    .state-back:hover {
        background: rgba(255, 255, 255, 0.38);
    }

    .state-content {
        color: white;
        max-width: 700px;
        display: flex;
        flex-direction: column;
    }

    .state-title-row {
        display: flex;
        align-items: center;
        gap: 15px;
        margin-bottom: 0.75rem;
    }

    .state-content h1 {
        font-family: 'Playfair Display', serif;
        font-size: 3rem;
        font-weight: 900;
        line-height: 1.1;
        margin-bottom: 0;
        text-shadow: 0 2px 12px rgba(0, 0, 0, 0.4);
    }

    .state-desc {
        font-size: 0.95rem;
        line-height: 1.6;
        color: rgba(255, 255, 255, 0.88);
        margin-bottom: 1.2rem;
        max-width: 560px;
    }

    .state-meta {
        display: flex;
        align-items: center;
        gap: 1.5rem;
        flex-wrap: wrap;
    }

    .state-tags {
        display: flex;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .state-tag {
        background: rgba(255, 255, 255, 0.18);
        backdrop-filter: blur(6px);
        border: 1px solid rgba(255, 255, 255, 0.3);
        color: white;
        padding: 0.3rem 0.9rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 500;
    }

    /* ── SEARCH BAR ── */
    .search-container {
        display: flex;
        justify-content: center;
        margin-top: 25px;
        padding-bottom: 1rem;
        background: white;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
        padding-top: 1rem;
    }

    .search-bar {
        background-color: var(--navy);
        padding: 0.45rem 0.45rem 0.45rem 1.5rem;
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
        font-size: 0.9rem;
    }

    .search-bar input::placeholder {
        color: #cbd5e1;
    }

    .search-bar button {
        background-color: white;
        color: var(--navy);
        border: none;
        padding: 0.55rem 1.8rem;
        border-radius: 30px;
        font-weight: bold;
        cursor: pointer;
    }

    /* ── DESTINATION SECTION ── */
    .destination-list {
        margin: 2rem 40px 0.5rem;
    }

    /* Section title row: title centered, circular arrow button on the right */
    .section-title-row {
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        margin-bottom: 1.25rem;
    }

    .section-title-row h1 {
        font-size: 22px;
        font-weight: 700;
        text-align: center;
    }

    /* Circular view-more arrow button */
    .view-more-circle {
        position: absolute;
        right: 0;
        width: 40px;
        height: 40px;
        border-radius: 50%;
        background: var(--navy);
        color: white;
        border: none;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        font-size: 1.1rem;
        box-shadow: 0 2px 8px rgba(26, 43, 73, 0.25);
        transition: background 0.2s, transform 0.15s;
        text-decoration: none;
        flex-shrink: 0;
    }

    .view-more-circle:hover {
        background: #243d6a;
        transform: scale(1.08);
    }

    /* 4-column grid */
    .destination-cards {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1.25rem;
    }

    /* ── DEST CARD ── */
    .dest-card {
        border: 1px solid var(--border-color);
        border-radius: 12px;
        overflow: hidden;
        background: white;
        display: flex;
        flex-direction: column;
        position: relative;
        cursor: pointer;
        transition: box-shadow 0.2s, transform 0.2s;
    }

    .dest-card:hover {
        box-shadow: 0 6px 20px rgba(0, 0, 0, 0.12);
        transform: translateY(-3px);
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
        font-size: 1.1rem;
        color: var(--text-muted);
        cursor: pointer;
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.12);
        transition: color 0.2s;
    }

    .heart-icon:hover {
        color: #ef4444;
    }

    .card-info {
        padding: 0.9rem;
        display: flex;
        flex-direction: column;
        flex-grow: 1;
    }

    .location {
        font-size: 0.72rem;
        color: var(--text-muted);
        margin-bottom: 0.2rem;
    }

    .dest-title {
        font-size: 0.9rem;
        font-weight: 600;
        margin-bottom: 0.4rem;
        line-height: 1.35;
        min-height: 2.5em;
        display: -webkit-box;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .climate {
        font-size: 0.78rem;
        color: var(--text-muted);
        margin-bottom: 0.75rem;
    }

    .card-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-top: auto;
        font-size: 0.82rem;
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
        font-size: 0.72rem;
    }

    .price {
        font-weight: 700;
    }

    /*View */
    .view_Btn {
        width: 50px;
        height: 50px;
        border-radius: 50%;
        border: none;
        background-color: #1A2B49;
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .view-icon {
        width: 22px;
        height: 22px;
        filter: brightness(0) invert(1);
    }



    /* ── REVIEWS SECTION ── */
    .reviews-section {
        margin: 2.5rem 40px 0;
    }

    .reviews-section>h1 {
        font-size: 22px;
        font-weight: 700;
        text-align: center;
        margin-bottom: 1.5rem;
    }

    .review-cards {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
    }

    .review-card.hidden {
        display: none;
    }

    .review-card {
        background: white;
        border: 1px solid var(--border-color);
        border-radius: 14px;
        padding: 1.25rem;
        display: flex;
        flex-direction: column;
        gap: 0.85rem;
        transition: box-shadow 0.2s;
    }

    .review-card:hover {
        box-shadow: 0 4px 18px rgba(0, 0, 0, 0.08);
    }

    .review-place {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding-bottom: 0.85rem;
        border-bottom: 1px solid var(--border-color);
    }

    .review-place-img {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        object-fit: cover;
        flex-shrink: 0;
    }

    .review-place-name {
        font-size: 0.88rem;
        font-weight: 700;
    }

    .review-place-rating {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        font-size: 0.78rem;
        margin-top: 0.15rem;
    }

    .review-place-rating .stars {
        color: #fbbf24;
    }

    .review-place-rating span {
        color: var(--text-muted);
    }

    .review-user {
        display: flex;
        align-items: center;
        gap: 0.65rem;
    }

    .review-avatar {
        width: 36px;
        height: 36px;
        border-radius: 50%;
        object-fit: cover;
        flex-shrink: 0;
        border: 2px solid var(--border-color);
    }

    .review-username {
        font-size: 0.85rem;
        font-weight: 600;
    }

    .review-date {
        font-size: 0.72rem;
        color: var(--text-muted);
    }

    .review-comment {
        font-size: 0.83rem;
        line-height: 1.6;
        color: #374151;
        display: -webkit-box;
        -webkit-line-clamp: 3;
        -webkit-box-orient: vertical;
        overflow: hidden;
    }

    .review-photos {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.15rem;
    }

    .review-photo {
        width: 56px;
        height: 56px;
        border-radius: 8px;
        object-fit: cover;
        flex-shrink: 0;
        border: 1px solid var(--border-color);
    }

    /* View More button (bottom, centered) */
    .view-more-wrap {
        display: flex;
        justify-content: center;
        margin-top: 2rem;
    }

    .view-more-btn {
        background: var(--navy);
        color: white;
        border: none;
        padding: 0.75rem 3rem;
        border-radius: 30px;
        font-size: 0.95rem;
        font-weight: 600;
        cursor: pointer;
        transition: background 0.2s, transform 0.15s;
    }

    .view-more-btn:hover {
        background: #243d6a;
        transform: translateY(-1px);
    }
</style>

<body>
    <?php include('./includes/navbar.php'); ?>

    <!-- ── STATE / HERO ── -->
    <section class="state">
    <div class="state-content">

        <!-- Back button + Title side by side -->
        <div class="state-title-row">
            <button type="button" class="state-back" onclick="window.location.href='recommendation-page.php'">&#8592;</button>
            <h1>Kuala Lumpur</h1>
        </div>

        <p class="state-desc">
            Malaysia's capital city blends modern skyscrapers with cultural heritage. From iconic landmarks to
            shopping districts and street food — KL is endlessly vibrant, buzzing with energy and excitement.
        </p>
        <div class="state-meta">
            <div class="state-tags">
                <span class="state-tag">🏙 Urban</span>
                <span class="state-tag">✨ Vibrant</span>
                <span class="state-tag">🌿 Lifestyle</span>
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
            <button type="button" class="view_Btn" onclick="window.location.href='tagging-type-management.php'">
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
            <button type="button" class="view_Btn" onclick="window.location.href='tagging-type-management.php'">
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
            <button type="button" class="view_Btn" onclick="window.location.href='tagging-type-management.php'">
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