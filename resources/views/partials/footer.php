<footer class="footer mt-auto py-5">
    <div class="container">
        <div class="row g-4">
            <div class="col-lg-4 col-md-6">
                <h5 class="footer-heading">Contact Us</h5>
                <address class="footer-contact">
                    <p><i class="fas fa-building me-2"></i>Coastal Fisheries Division<br>
                    Ministry of Fisheries and Ocean Resources<br>
                    Bairiki, Tarawa, Kiribati</p>
                    <p><i class="fas fa-phone me-2"></i>+686 75021099</p>
                    <p><i class="fas fa-envelope me-2"></i>infor@mfor.gov.ki</p>
                </address>
            </div>
            <div class="col-lg-4 col-md-6">
                <h5 class="footer-heading">Follow Us</h5>
                <div class="social-icons">
                    <a href="https://www.facebook.com/KirMFOR/" class="social-icon"><i class="fab fa-facebook-f"></i></a>
                    <a href="#" class="social-icon"><i class="fab fa-twitter"></i></a>
                    <a href="https://www.linkedin.com/company/ministry-of-fisheries-and-marine-resources-development/about/" class="social-icon"><i class="fab fa-linkedin-in"></i></a>
                </div>
            </div>
            <div class="col-lg-4 col-md-12">
            <img src="http://localhost/fisherylicense/public/images/logos.png" alt="Official Seal" class="logo">
                <p class="footer-description">Serving the people of Kiribati through sustainable fisheries management and marine resource development.</p>
            </div>
        </div>
        <hr class="footer-divider">
        <div class="row footer-bottom">
            <div class="col-md-8">
            <p class="copyright">© <?php echo date('Y'); ?> Coastal Fisheries Division, Ministry of Fisheries and Ocean Resources. All rights reserved.</p>

            </div>
            <div class="col-md-4">
                <div class="footer-links">
                    <a href="#">Privacy Policy</a>
                    <span class="separator">|</span>
                    <a href="#">Terms of Service</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<style>
/* Make sure to include Font Awesome in your project */
@import url('https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css');

.footer {
    background: linear-gradient(to right, #1a3c6d, #2a5298);
    color: #ffffff;
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    position: relative;
    box-shadow: 0 -10px 20px rgba(0,0,0,0.1);
}

.footer::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    height: 3px;
    background: linear-gradient(to right, #ffd700, #ff9900);
}

.footer-heading {
    color: #ffffff;
    font-size: 1.25rem;
    font-weight: 600;
    margin-bottom: 1.5rem;
    position: relative;
    padding-bottom: 0.75rem;
}

.footer-heading::after {
    content: '';
    position: absolute;
    left: 0;
    bottom: 0;
    width: 50px;
    height: 2px;
    background: #ffd700;
}

.footer-contact {
    font-style: normal;
    line-height: 1.8;
}

.footer-contact p {
    margin-bottom: 0.75rem;
    display: flex;
    align-items: center;
}

.footer-contact i {
    color: #ffd700;
    width: 20px;
}

.social-icons {
    display: flex;
    gap: 1rem;
    margin-top: 1rem;
}

.social-icon {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: rgba(255,255,255,0.1);
    color: #ffffff;
    transition: all 0.3s ease;
}

.social-icon:hover {
    background: #ffd700;
    color: #1a3c6d;
    transform: translateY(-3px);
}

.footer-logo {
    max-height: 80px;
    margin-bottom: 1rem;
}

.footer-description {
    color: rgba(255,255,255,0.8);
    line-height: 1.6;
}

.footer-divider {
    margin: 2rem 0;
    border-color: rgba(255,255,255,0.1);
}

.footer-bottom {
    padding-top: 1rem;
}

.copyright {
    color: rgba(255,255,255,0.8);
    font-size: 0.9rem;
    margin: 0;
}

.footer-links {
    display: flex;
    justify-content: flex-end;
    gap: 1rem;
    align-items: center;
}

.footer-links a {
    color: rgba(255,255,255,0.8);
    text-decoration: none;
    font-size: 0.9rem;
    transition: color 0.3s ease;
}

.footer-links a:hover {
    color: #ffd700;
}

.separator {
    color: rgba(255,255,255,0.4);
}

@media (max-width: 768px) {
    .footer {
        text-align: center;
    }
    
    .footer-heading::after {
        left: 50%;
        transform: translateX(-50%);
    }
    
    .social-icons {
        justify-content: center;
    }
    
    .footer-links {
        justify-content: center;
        margin-top: 1rem;
    }
    
    .footer-contact p {
        justify-content: center;
    }

   /* Container for logo and text */
.logo-container {
    display: flex;
    align-items: center;
    gap: 10px; /* Space between logo and text */
}

/* Adjusting the size of the logo */
.logo {
    max-width: 40px; /* Very small logo size */
    height: auto; /* Maintain aspect ratio */
}

/* Adjusting the footer description */
.footer-description {
    font-size: 0.9rem; /* Font size adjustment */
    color: rgba(255, 255, 255, 0.8);
    line-height: 1.6;
    margin: 0;
}


}

}
</style>