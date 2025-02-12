<?php
$meta = "404 - Page Not Found";
?>
<style>
  .auth-container {
    max-width: 350px !important;
  }

  h1 {
    font-size: 6rem;
    color: #121212;
    animation: bounce 1s infinite alternate;
    text-align: center;
  }

  p {
    font-size: 1.2rem;
    margin: 10px 0 20px;
    text-align: center;
  }

  a {
    display: inline-block;
    text-decoration: none;
    background: #121212;
    color: white;
    padding: 10px 20px;
    border-radius: 5px;
    transition: 0.3s;
    text-align: center;
  }

  a:hover {
    background: #e84118;
  }

  @keyframes bounce {
    from {
      transform: translateY(0);
    }

    to {
      transform: translateY(-10px);
    }
  }
</style>
<div class="auth-container">
  <h1>404</h1>
  <p>Oops! The page you're looking for doesn't exist.</p>
  <a href="<?= BASE_PATH ?>">Go Home</a>
</div>