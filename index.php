<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">

<title>Converge</title>

<link rel="stylesheet" href="style1.css">

</head>

<body>

<?php require_once 'includes/header.php'; ?>

<main class="home-main">

<!-- HERO SECTION -->

<section class="hero">

<div class="hero-content">

<span class="hero-eyebrow">Welcome to Converge</span>

<h1 class="hero-title">
Find Friends<br>
<em>With Similar</em><br>
Interests
</h1>

<p class="hero-sub">
Discover people who share your hobbies, explore new places,
and plan exciting meetups together.
</p>

<div class="hero-actions">

<?php if(isset($_SESSION["user_id"])) { ?>

<a href="/Converge-Meet-App-main/user/dashboard.php" class="btn btn-primary btn-lg">
Go to Dashboard
</a>

<?php } else { ?>

<a href="/Converge-Meet-App-main/auth/register.php" class="btn btn-primary btn-lg">
Get Started
</a>

<a href="/Converge-Meet-App-main/auth/login.php" class="btn btn-gold btn-lg">
Login
</a>

<?php } ?>

</div>

</div>


<div class="hero-accent">

<div class="accent-circle accent-circle--red"></div>
<div class="accent-circle accent-circle--gold"></div>
<div class="accent-dot"></div>

</div>

</section>


<!-- FEATURES -->

<section class="features">

<div class="features-header">

<h2 class="section-title">
How <em>Converge</em> Works
</h2>

<p class="section-sub">
Three simple steps to meaningful connections
</p>

</div>


<div class="features-grid">

<div class="feature-card">

<div class="feature-icon feature-icon--red">

<svg width="28" height="28" viewBox="0 0 24 24"
fill="none" stroke="currentColor" stroke-width="2">

<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
<circle cx="12" cy="7" r="4"/>

</svg>

</div>

<h3 class="feature-title">Build Your Profile</h3>

<p class="feature-desc">
Set up your profile and list your hobbies, passions,
and the places you love to visit.
</p>

</div>


<div class="feature-card">

<div class="feature-icon feature-icon--gold">

<svg width="28" height="28" viewBox="0 0 24 24"
fill="none" stroke="currentColor" stroke-width="2">

<circle cx="11" cy="11" r="8"/>
<line x1="21" y1="21" x2="16.65" y2="16.65"/>

</svg>

</div>

<h3 class="feature-title">Discover People</h3>

<p class="feature-desc">
Our interest matching system finds people near you
who share your exact passions.
</p>

</div>


<div class="feature-card">

<div class="feature-icon feature-icon--red">

<svg width="28" height="28" viewBox="0 0 24 24"
fill="none" stroke="currentColor" stroke-width="2">

<path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/>
<circle cx="12" cy="10" r="3"/>

</svg>

</div>

<h3 class="feature-title">Plan Meetups</h3>

<p class="feature-desc">
Get smart place suggestions tailored to your interests
and organize real world events.
</p>

</div>

</div>

</section>


<!-- CTA -->

<section class="cta-banner">

<div class="cta-inner">

<h2 class="cta-title">
Ready to <em>Converge?</em>
</h2>

<p class="cta-sub">
Join hundreds of people already finding their community.
</p>

<a href="/Converge-Meet-App-main/auth/register.php"
class="btn btn-primary btn-lg">

Create Free Account

</a>

</div>

</section>


</main>

<?php require_once 'includes/footer.php'; ?>