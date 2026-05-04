<?php
if (session_status() === PHP_SESSION_NONE) session_start();
// User session is set by process-login.php
$is_logged_in = !empty($_SESSION['logged_in']);
$user_name = $_SESSION['user_name'] ?? '';
$user_email = $_SESSION['user_email'] ?? '';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hostel_system";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create table if not exists
$sql = "CREATE TABLE IF NOT EXISTS bookings (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100),
    email VARCHAR(100),
    phone VARCHAR(20),
    room_type VARCHAR(50),
    message TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
)";
$conn->query($sql);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $room_type = $_POST['room_type'];
    $message = $_POST['message'];

    $stmt = $conn->prepare("INSERT INTO bookings (name, email, phone, room_type, message) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("sssss", $name, $email, $phone, $room_type, $message);
    if ($stmt->execute()) {
        $success = "✅ Booking successful!";
    } else {
        $error = "❌ Error: " . $stmt->error;
    }
    $stmt->close();
}
?>
<!doctype html>
<html lang="en">
    <head>
        <!-- Required meta tags -->
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
        <link rel="icon" href="image/favicon.png" type="image/png">
        <title>StayEasy</title>
        <!-- Bootstrap CSS -->
        <link rel="stylesheet" href="css/bootstrap.css">
        <link rel="stylesheet" href="vendors/linericon/style.css">
        <link rel="stylesheet" href="css/font-awesome.min.css">
        <link rel="stylesheet" href="vendors/owl-carousel/owl.carousel.min.css">
        <link rel="stylesheet" href="vendors/bootstrap-datepicker/bootstrap-datetimepicker.min.css">
        <link rel="stylesheet" href="vendors/nice-select/css/nice-select.css">
        <link rel="stylesheet" href="vendors/owl-carousel/owl.carousel.min.css">
        <!-- main css -->
        <link rel="stylesheet" href="css/style.css">
        <link rel="stylesheet" href="css/responsive.css">
        <style>
        /* Profile icon and compact dropdown */
        .nav-profile { position: relative; }
        .profile-toggle { display:flex; align-items:center; gap:8px; cursor:pointer; }
        .profile-circle { width:34px; height:34px; border-radius:50%; background:#f1f2f6; display:inline-flex; align-items:center; justify-content:center; color:#333; font-size:16px; border:1px solid rgba(0,0,0,0.06); }
        .nav-profile-dropdown { position:absolute; right:0; top:44px; width:180px; background:#fff; border-radius:8px; box-shadow:0 8px 24px rgba(40,40,80,0.08); overflow:hidden; transform-origin:top right; transform: translateY(-8px) scale(0.98); opacity:0; pointer-events:none; transition:transform .22s cubic-bezier(.2,.9,.2,1), opacity .18s ease; z-index:9999; }
        .nav-profile-dropdown.show { transform: translateY(0) scale(1); opacity:1; pointer-events:auto; }
        .nav-profile-dropdown a, .nav-profile-dropdown .nav-label { display:block; padding:10px 12px; color:#333; text-decoration:none; font-size:14px; border-bottom:1px solid #f3f4f6; }
        .nav-profile-dropdown a:hover { background:#f7f9ff; color:#222; }
        .nav-profile-dropdown .nav-label { font-weight:600; color:#555; background:transparent; cursor:default; }
        .nav-profile-dropdown .small-muted { font-size:12px; color:#888; font-weight:400; padding-top:0; }
        /* small caret */
        .profile-caret { width:0;height:0;border-left:5px solid transparent;border-right:5px solid transparent;border-bottom:6px solid #fff; position:absolute; right:12px; top:36px; display:none; }
        @media (max-width:991px){ .nav-profile-dropdown{ right:10px; } }
        /* make login link match other nav links */
        .menu_nav .nav-link.login-link { background: none !important; color: inherit !important; padding: 0.4375rem 0.75rem; border-radius: 0; }
        </style>
        <style>
        /* Basic styling for the banner area */
        .banner-area {
            width: 100%;
            max-width: 800px;
            margin: 20px auto;
            text-align: center;
            padding: 20px;
            border: 1px solid #ccc;
            background-color: #f9f9f9;
        }

        /* Styling for the button */
        #get-start-button {
            padding: 10px 20px;
            font-size: 18px;
            background-color: #ff0000; /* YouTube Red */
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            margin-bottom: 20px;
        }

        #get-start-button:hover {
            background-color: #cc0000;
        }

        /* The video container, initially hidden or styled as desired */
        #player {
            width: 100%;
            height: 450px; /* Adjust height as needed */
            display: none; /* Hide the video player initially */
        }
    </style>
    <style>
/* CRITICAL: Ensure the banner section is a positioning context */
.banner_area {
    position: relative;
    overflow: hidden; 
}

/* Styles for the Background Video Element */
#bg-banner-video {
    position: absolute;
    top: 50%;
    left: 50%;
    min-width: 100%;
    min-height: 100%;
    width: auto;
    height: auto;
    z-index: 1; /* Puts the video behind the overlay and content */
    transform: translate(-50%, -50%); /* Centers the video */
    object-fit: cover; /* Ensures the video covers the entire section */
}

/* Ensure the overlay and content are positioned correctly above the video */
.overlay {
    z-index: 2; /* Over the video */
    background: rgba(0, 0, 0, 0.4); /* Add darkness for contrast */
}

.banner_content {
    position: relative;
    z-index: 4; /* Highest z-index for text and buttons */
    color: white; /* Ensure text is visible */
}
</style>
    </head>
    <body>
        <!--================Header Area =================-->
        <header class="header_area">
            <div class="container">
                <nav class="navbar navbar-expand-lg navbar-light">
                    <!-- Brand and toggle get grouped for better mobile display -->
                    <a class="navbar-brand logo_h" href="index.php"><img src="image/Logo.png" alt=""></a>
                    <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                        <span class="icon-bar"></span>
                    </button>
                    <!-- Collect the nav links, forms, and other content for toggling -->
                    <div class="collapse navbar-collapse offset" id="navbarSupportedContent">
                        <ul class="nav navbar-nav menu_nav ml-auto">
                            <li class="nav-item active"><a class="nav-link" href="index.php">Home</a></li> 
                            <li class="nav-item"><a class="nav-link" href="about.html">About us</a></li>
                            <li class="nav-item"><a class="nav-link" href="accomodation.html">Accomodation</a></li>
                            <li class="nav-item"><a class="nav-link" href="gallery.html">Gallery</a></li>
                            <li class="nav-item"><a class="nav-link" href="hostel-maintenance.html">Maintenance</a></li>
                            <li class="nav-item submenu dropdown">
                                <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">Blog</a>
                                <ul class="dropdown-menu">
                                    <li class="nav-item"><a class="nav-link" href="blog.html">Blog</a></li>
                                </ul>
                            </li>
                            <li class="nav-item"><a class="nav-link" href="contact.html">Contact</a></li>
                            <?php if ($is_logged_in): ?>
                                <li class="nav-item nav-profile">
                                    <a class="nav-link profile-toggle" href="#" id="profileToggle" aria-haspopup="true" aria-expanded="false">
                                        <span class="profile-circle"><i class="fa fa-user"></i></span>
                                        <span class="small text-muted d-none d-lg-inline">Account</span>
                                        <span class="dropdown-caret d-none d-lg-inline"><i class="fa fa-caret-down"></i></span>
                                    </a>
                                    <div class="nav-profile-dropdown" id="profileDropdown" role="menu" aria-labelledby="profileToggle">
                                        <div class="nav-label">Hello, <?php echo htmlspecialchars($user_name); ?></div>
                                        <a href="#" class="dropdown-item">Notifications</a>
                                        <a href="#" class="dropdown-item">Settings</a>
                                        <a href="logout.php" class="dropdown-item">Logout</a>
                                    </div>
                                </li>
                            <?php else: ?>
                                <li class="nav-item"><a class="nav-link login-link" href="user-login.html">Login</a></li>
                            <?php endif; ?>
                        </ul>
                    </div> 
                </nav>
            </div>
        </header>
        <!--================Header Area =================-->
        
       <section class="banner_area" style="position: relative; overflow: hidden;">
    
    <video autoplay loop muted playsinline id="bg-banner-video">
        <source src="image\video (1).mp4" type="video/mp4">
        Your browser does not support the video tag.
    </video>
    
    <div class="booking_table d_flex align-items-center">
        <div class="overlay bg-parallax" data-stellar-ratio="0.9" data-stellar-vertical-offset="0" data-background="" style="z-index: 2;"></div>
        <div class="container" style="position: relative; z-index: 3;">
            <div class="banner_content text-center">
                <h6>Escape the Ordinary</h6>
                <h2>Find Your Perfect Stay</h2>
                <p>Tired of endless calls and messy bookings? Our Hostel Booking and Management System makes finding, reserving, and managing rooms simple, fast, and stress-free.<br> Enjoy seamless check-ins, real-time availability, and hassle-free stays—all in one place.</p>
                <a href="#" id="get-start-button" class="btn theme_btn button_hover">Get Started</a>
                </div>
        </div>
    </div>
</section>
           
  <!--================ Facilities Area  =================-->
        <section class="facilities_area section_gap">
            <div class="overlay bg-parallax" data-stellar-ratio="0.8" data-stellar-vertical-offset="0" data-background="">  
            </div>
            <div class="container">
                <div class="section_title text-center">
                    <h2 class="title_w">Hostel Facilities</h2>
                    <p>Who are in extremely love with eco friendly system.</p>
                </div>
                <div class="row mb_30">
                    <div class="col-lg-4 col-md-6">
                        <div class="facilities_item">
                            <h4 class="sec_h4"><i class="lnr lnr-van"></i>Attached Bathroom</h4>
                            <p>Every room is equipped with a modern private bathroom featuring:</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="facilities_item">
                            <h4 class="sec_h4"><i class="lnr lnr-van"></i>High-Speed Wi-Fi</h4>
                            <p>Reliable and fast internet available in all rooms and common areas to support online classes, research, and entertainment.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="facilities_item">
                            <h4 class="sec_h4"><i class="lnr lnr-van"></i>Common Areas & Study Spaces</h4>
                            <p>Common Lounge Area Dedicated Study Room.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="facilities_item">
                            <h4 class="sec_h4"><i class="lnr lnr-van"></i>Dining Area</h4>
                            <p>A clean and spacious dining hall where students can enjoy meals comfortably.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="facilities_item">
                            <h4 class="sec_h4"><i class="lnr lnr-van"></i>24/7 Security</h4>
                            <p>Security guards available around the clock.</p>
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="facilities_item">
                            <h4 class="sec_h4"><i class="lnr lnr-coffee-"></i>CCTV Surveillance</h4>
                            <p>All common areas and entry points are monitored for student safety.</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--================ Facilities Area  =================-->
        
        <!--================ About History Area  =================-->
        <section class="about_history_area section_gap">
            <div class="container">
                <div class="row">
                    <div class="col-md-6 d_flex align-items-center">
                        <div class="about_content ">
                            <h2 class="title title_color">About Us <br>Page Content<br>Our Story</h2>
                            <p>SLTC Hostel was created to offer students a comfortable and secure 
                                living environment. We blend modern facilities with a friendly community atmosphere, 
                                ensuring every resident enjoys a positive and productive hostel life.</p>

                                <h2 class="title title_color">our Vision <br></h2>
                                <p>To become the most trusted student accommodation provider through technology-driven 
                                    solutions and exceptional on-site facilities.</p>

                                <h2 class="title title_color">our Mision <br></h2>
`                                 <p>Provide clean, safe, and affordable living spaces.
                                    <br> room booking and management simple through digital tools.

                                    <br> Support student well-being with healthy, accessible facilities.</p>


                           
                        </div>
                    </div>
                    <div class="col-md-6">
                        <img class="img-fluid" src="image/about_bg.jpg" alt="img" width="500px" height="400px">
                    </div>
                </div>
            </div>
        </section>
        <!--================ About History Area  =================-->
        
     <!--================ Testimonial Area  =================-->
        <section class="testimonial_area section_gap">
            <div class="container">
                <div class="section_title text-center">
                    <h2 class="title_color">Feedback from our Users</h2>
                    <p>Here’s what our users say about their experience with our hostel management system.</p>
                </div>
                <div class="testimonial_slider owl-carousel">
                    <div class="media testimonial_item">
                        <img class="rounded-circle" src="image/testtimonial-1.jpg" alt="">
                        <div class="media-body">
                            <p>"Easy to use and very efficient! Managing hostel rooms and payments has become so simple. Highly recommended!"</p>
                            <a href="#"><h4 class="sec_h4">Vishwa Nuwan</h4></a>
                            <div class="star">
                                <a href="#"><i class="fa fa-star"></i></a>
                                <a href="#"><i class="fa fa-star"></i></a>
                                <a href="#"><i class="fa fa-star"></i></a>
                                <a href="#"><i class="fa fa-star"></i></a>
                                <a href="#"><i class="fa fa-star-half-o"></i></a>
                            </div>
                        </div>
                    </div>    
                    <div class="media testimonial_item">
                        <img class="rounded-circle" src="image/testtimonial-2.jpg" alt="">
                        <div class="media-body">
                            <p>"A clean, user-friendly system that saves a lot of time. Perfect for hostel owners looking for smooth management." </p>
                            <a href="#"><h4 class="sec_h4">Peshala Sandeepani</h4></a>
                            <div class="star">
                                <a href="#"><i class="fa fa-star"></i></a>
                                <a href="#"><i class="fa fa-star"></i></a>
                                <a href="#"><i class="fa fa-star"></i></a>
                                <a href="#"><i class="fa fa-star"></i></a>
                                <a href="#"><i class="fa fa-star-half-o"></i></a>
                            </div>
                        </div>
                    </div>
                    <div class="media testimonial_item">
                        <img class="rounded-circle" src="image/testtimonial-3.jpg" alt="">
                        <div class="media-body">
                            <p>Great design and smooth performance. The QR code feature makes check-ins much easier!” </p>
                            <a href="#"><h4 class="sec_h4">Gimhan Kanishka</h4></a>
                            <div class="star">
                                <a href="#"><i class="fa fa-star"></i></a>
                                <a href="#"><i class="fa fa-star"></i></a>
                                <a href="#"><i class="fa fa-star"></i></a>
                                <a href="#"><i class="fa fa-star"></i></a>
                                <a href="#"><i class="fa fa-star-half-o"></i></a>
                            </div>
                        </div>
                    </div>    
                    <div class="media testimonial_item">
                        <img class="rounded-circle" src="image/testtimonial-4.jpg" alt="">
                        <div class="media-body">
                            <p>“Everything is well organized. A perfect tool for managing students, rooms, and payments.”</p>
                            <a href="#"><h4 class="sec_h4">Chamindu Nimesh</h4></a>
                            <div class="star">
                                <a href="#"><i class="fa fa-star"></i></a>
                                <a href="#"><i class="fa fa-star"></i></a>
                                <a href="#"><i class="fa fa-star"></i></a>
                                <a href="#"><i class="fa fa-star"></i></a>
                                <a href="#"><i class="fa fa-star-half-o"></i></a>
                            </div>
                        </div>
                    </div>
                     <div class="media testimonial_item">
                        <img class="rounded-circle" src="image/testtimonial-5.jpg" alt="">
                        <div class="media-body">
                            <p>“Everything is well organized. A perfect tool for managing students, rooms, and payments.”</p>
                            <a href="#"><h4 class="sec_h4">Naduni Dilthusha</h4></a>
                            <div class="star">
                                <a href="#"><i class="fa fa-star"></i></a>
                                <a href="#"><i class="fa fa-star"></i></a>
                                <a href="#"><i class="fa fa-star"></i></a>
                                <a href="#"><i class="fa fa-star"></i></a>
                                <a href="#"><i class="fa fa-star-half-o"></i></a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--================ Testimonial Area  =================-->
        
        <!--================ Latest Blog Area  =================-->
        <section class="latest_blog_area section_gap">
            <div class="container">
                <div class="section_title text-center">
                    <h2 class="title_color">latest posts from blog</h2>
                    <p>The French Revolution constituted for the conscience of the dominant aristocratic class a fall from </p>
                </div>
                <div class="row mb_30">
                    <div class="col-lg-4 col-md-6">
                        <div class="single-recent-blog-post">
                            <div class="thumb">
                                <img class="img-fluid" src="image/blog/blog-1.jpg" alt="post">
                            </div>
                            <div class="details">
                                <div class="tags">
                                    <a href="#" class="button_hover tag_btn">Travel</a>
                                    <a href="#" class="button_hover tag_btn">Life Style</a>
                                </div>
                                <a href="#"><h4 class="sec_h4">Low Cost Advertising</h4></a>
                                <p>Acres of Diamonds… you’ve read the famous story, or at least had it related to you. A farmer.</p>
                                <h6 class="date title_color">31st January,2018</h6>
                            </div>	
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="single-recent-blog-post">
                            <div class="thumb">
                                <img class="img-fluid" src="image/blog/blog-2.jpg" alt="post">
                            </div>
                            <div class="details">
                                <div class="tags">
                                    <a href="#" class="button_hover tag_btn">Travel</a>
                                    <a href="#" class="button_hover tag_btn">Life Style</a>
                                </div>
                                <a href="#"><h4 class="sec_h4">Creative Outdoor Ads</h4></a>
                                <p>Self-doubt and fear interfere with our ability to achieve or set goals. Self-doubt and fear are</p>
                                <h6 class="date title_color">31st January,2018</h6>
                            </div>	
                        </div>
                    </div>
                    <div class="col-lg-4 col-md-6">
                        <div class="single-recent-blog-post">
                            <div class="thumb">
                                <img class="img-fluid" src="image/blog/blog-3.jpg" alt="post">
                            </div>
                            <div class="details">
                                <div class="tags">
                                    <a href="#" class="button_hover tag_btn">Travel</a>
                                    <a href="#" class="button_hover tag_btn">Life Style</a>
                                </div>
                                <a href="#"><h4 class="sec_h4">It S Classified How To Utilize Free</h4></a>
                                <p>Why do you want to motivate yourself? Actually, just answering that question fully can </p>
                                <h6 class="date title_color">31st January,2018</h6>
                            </div>	
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--================ Recent Area  =================-->
        
        <!--================ start footer Area  =================-->	
        <footer class="footer-area section_gap">
            <div class="container">
                <div class="row">
                    <div class="col-lg-3  col-md-6 col-sm-6">
                        <div class="single-footer-widget">
                            <h6 class="footer_title">About Hostel</h6>
                            <p>Our hostel combines comfort, safety, and convenience to give students the best living experience. With quality facilities and a supportive environment, it is the ideal place to stay during your university years. </p>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="single-footer-widget">
                            <h6 class="footer_title">Navigation Links</h6>
                            <div class="row">
                                <div class="col-4">
                                    <ul class="list_style">
                                        <li><a href="index.php">Home</a></li>
                                        <li><a href="about.html">About us</a></li>
                                        <li><a href="gallery.html">gallery</a></li>
                                        <li><a href="blog-single.html">Blog</a></li>
                                    </ul>
                                </div>
                                <div class="col-4">
                                    <ul class="list_style">
                            
                                        <li><a href="blog.html">Blog</a></li>
                                        <li><a href="contact.html">Contact</a></li>
                                    </ul>
                                </div>										
                            </div>							
                        </div>
                    </div>							
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="single-footer-widget">
                            <h6 class="footer_title">Newsletter</h6>
                            <p>Our hostel creates a homely space where students can live, learn, and grow together </p>		
                            <div id="mc_embed_signup">
                                <form target="_blank" action="https://spondonit.us12.list-manage.com/subscribe/post?u=1462626880ade1ac87bd9c93a&amp;id=92a4423d01" method="get" class="subscribe_form relative">
                                    <div class="input-group d-flex flex-row">
                                        <input name="EMAIL" placeholder="Email Address" onfocus="this.placeholder = ''" onblur="this.placeholder = 'Email Address '" required="" type="email">
                                        <button class="btn sub-btn"><span class="lnr lnr-location"></span></button>		
                                    </div>									
                                    <div class="mt-10 info"></div>
                                </form>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6 col-sm-6">
                        <div class="single-footer-widget instafeed">
                            <h6 class="footer_title">InstaFeed</h6>
                            <ul class="list_style instafeed d-flex flex-wrap">
                                <li><img src="image/instagram/Image-01.jpg" alt="" width="70px"></li>
                                <li><img src="image/instagram/Image-02.jpg" alt="" width="70px"></li>
                                <li><img src="image/instagram/Image-03.jpg" alt="" width="70px"></li>
                                <li><img src="image/instagram/Image-04.jpg" alt="" width="70px"></li>
                                <li><img src="image/instagram/Image-05.jpg" alt="" width="70px"></li>
                                <li><img src="image/instagram/Image-06.jpg" alt="" width="70px"></li>
                                <li><img src="image/instagram/Image-07.jpg" alt="" width="70px"></li>
                                <li><img src="image/instagram/Image-08.jpg" alt="" width="70px"></li>
                            </ul>
                        </div>
                    </div>						
                </div>
                <div class="border_line"></div>
                <div class="row footer-bottom d-flex justify-content-between align-items-center">
                    <p class="col-lg-8 col-sm-12 footer-text m-0"><!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. -->
Copyright &copy;<script>document.write(new Date().getFullYear());</script> All rights reserved | This website made with <i class="fa fa-heart-o" aria-hidden="true"></i> Group 15 
<!-- Link back to Colorlib can't be removed. Template is licensed under CC BY 3.0. --></p>
                    <div class="col-lg-4 col-sm-12 footer-social">
                        <a href="#"><i class="fa fa-facebook"></i></a>
                        <a href="#"><i class="fa fa-twitter"></i></a>
                        <a href="#"><i class="fa fa-dribbble"></i></a>
                        <a href="#"><i class="fa fa-behance"></i></a>
                    </div>
                </div>
            </div>
        </footer>
		<!--================ End footer Area  =================-->
        
        
        <!-- Optional JavaScript -->
        <!-- jQuery first, then Popper.js, then Bootstrap JS -->
        <script src="js/jquery-3.2.1.min.js"></script>
        <script src="js/popper.js"></script>
        <script src="js/bootstrap.min.js"></script>
        <script src="vendors/owl-carousel/owl.carousel.min.js"></script>
        <script src="js/jquery.ajaxchimp.min.js"></script>
        <script src="js/mail-script.js"></script>
        <script src="vendors/bootstrap-datepicker/bootstrap-datetimepicker.min.js"></script>
        <script src="vendors/nice-select/js/jquery.nice-select.js"></script>
        <script src="js/mail-script.js"></script>
        <script src="js/stellar.js"></script>
        <script src="vendors/lightbox/simpleLightbox.min.js"></script>
        <script src="js/custom.js"></script>
        <script>
        // Profile dropdown toggle
        (function(){
            var toggle = document.getElementById('profileToggle');
            var dropdown = document.getElementById('profileDropdown');
            if (!toggle || !dropdown) return;
            function hide(){ dropdown.classList.remove('show'); }
            function show(){ dropdown.classList.add('show'); }
            toggle.addEventListener('click', function(e){
                e.preventDefault();
                dropdown.classList.toggle('show');
            });
            // close when clicking outside
            document.addEventListener('click', function(e){
                if (!dropdown.contains(e.target) && !toggle.contains(e.target)) {
                    hide();
                }
            });
            // close on escape
            document.addEventListener('keydown', function(e){ if (e.key === 'Escape') hide(); });
        })();
        </script>
    </body>
</html>












<!--

 <div class="hotel_booking_area position">
                <div class="container">
                    <div class="hotel_booking_table">
                        <div class="col-md-3">
                            <h2>Book<br> Your Room</h2>
                        </div>
                        <div class="col-md-9">
                            <div class="boking_table">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="book_tabel_item">
                                            <div class="form-group">
                                                <div class='input-group date' id='datetimepicker11'>
                                                    <input type='text' class="form-control" placeholder="Arrival Date"/>
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                                    </span>
                                                </div>
                                            </div>
                                            <div class="form-group">
                                                <div class='input-group date' id='datetimepicker1'>
                                                    <input type='text' class="form-control" placeholder="Departure Date"/>
                                                    <span class="input-group-addon">
                                                        <i class="fa fa-calendar" aria-hidden="true"></i>
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="book_tabel_item">
                                            <div class="input-group">
                                                <select class="wide">
                                                    <option data-display="Adult">Adult</option>
                                                    <option value="1">Old</option>
                                                    <option value="2">Younger</option>
                                                    <option value="3">Potato</option>
                                                </select>
                                            </div>
                                            <div class="input-group">
                                                <select class="wide">
                                                    <option data-display="Child">Child</option>
                                                    <option value="1">Child</option>
                                                    <option value="2">Baby</option>
                                                    <option value="3">Child</option>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-4">
                                        <div class="book_tabel_item">
                                            <div class="input-group">
                                                <select class="wide">
                                                    <option data-display="Child">Number of Rooms</option>
                                                    <option value="1">Room 01</option>
                                                    <option value="2">Room 02</option>
                                                    <option value="3">Room 03</option>
                                                </select>
                                            </div>
                                            
<form method="POST" action="">
    <div class="form-group">
        <input type="text" name="name" class="form-control" placeholder="Full Name" required>
    </div>
    <div class="form-group">
        <input type="email" name="email" class="form-control" placeholder="Email Address" required>
    </div>
    <div class="form-group">
        <input type="text" name="phone" class="form-control" placeholder="Phone Number" required>
    </div>
    <div class="form-group">
        <select name="room_type" class="wide">
            <option value="Double Deluxe Room">Double Deluxe Room</option>
            <option value="Single Deluxe Room">Single Deluxe Room</option>
            <option value="Honeymoon Suit">Honeymoon Suit</option>
            <option value="Economy Double">Economy Double</option>
        </select>
    </div>
    <div class="form-group">
        <textarea name="message" class="form-control" placeholder="Special Requests"></textarea>
    </div>
    <button type="submit" class="btn theme_btn button_hover">Book Now</button>
</form>
<?php if(isset($success)) echo "<p style='color:green;'>$success</p>"; ?>
<?php if(isset($error)) echo "<p style='color:red;'>$error</p>"; ?>

                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>
        <!--================Banner Area =================-->
        
