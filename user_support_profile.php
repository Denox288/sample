<style>
    .user-profile {
        display: flex;
        font-family: 'Segoe UI', sans-serif;
        color: #2d3e50;
    }

    .profile-card {
        background: #fff;
        padding: 24px;
        border-radius: 16px;
        width: 320px;
        text-align: center;
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .profile-card img {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 4px solid #3498db;
        object-fit: cover;
        margin-bottom: 16px;
    }

    .profile-card h3 {
        font-size: 1.5rem;
        margin: 8px 0 4px;
        color: #34495e;
    }

    .profile-card h5 {
        font-size: 1rem;
        font-weight: 500;
        margin: 0;
        color: #7f8c8d;
    }

    .profile-card p {
        margin: 12px 0;
        font-size: 0.95rem;
        color: #95a5a6;
    }

    .profile-card h6 {
        font-size: 0.9rem;
        color: #aaa;
        margin-bottom: 16px;
    }

    .user-welcome {
        margin-top: 30px;
        text-align: center;
    }

    .user-welcome h2 {
        font-size: 1.5rem;
        margin-bottom: 4px;
        color: #2c3e50;
    }

    .user-welcome p {
        font-size: 1rem;
        color: #7f8c8d;
    }

    @media (max-width: 400px) {
        .profile-card {
            width: 90%;
        }
    }
</style>

<div class="user-profile">
    <div class="user-welcome">
    </div>
    <div class="profile-card">
        <div style="display: flex; align-items: center; justify-content: center;">
            <img src="css/img/cat.png" alt="User Photo" style="margin-right: 20px;">
            <div style="text-align: left;">
                <h2 style="margin: 0;">Hello <?= htmlspecialchars($loggedInUser['UserName']) ?>!</h2>
                <p style="margin: 4px 0 0;"><?= htmlspecialchars($loggedInUser['DEPT']) ?></p>
            </div>
        </div>
    </div>
</div>
