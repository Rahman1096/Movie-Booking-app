<!-- Add this link to the <head> section to ensure the Bootstrap Icons are loaded -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<!-- Footer -->

<footer id="footer" class="footer" style="background: #007bff; padding: 10px 0; border-top: 1px solid #e0e0e0; color: #fff;">

    <div class="container py-3">

        <div class="text-center">

            <div class="copyright">
                <?php  
                $year = date('Y');
                ?>
                © Copyright <?= $year ?>. <strong><span class="brand-name">CinemaBuddy</span></strong>. All Rights Reserved.
            </div>

            <div class="social-links mt-2">
                <a href="https://facebook.com" target="_blank" class="bi bi-facebook" title="Facebook" style="color: #fff; margin: 0 10px;"></a>
                <a href="https://twitter.com" target="_blank" class="bi bi-twitter" title="Twitter" style="color: #fff; margin: 0 10px;"></a>
                <a href="https://youtube.com" target="_blank" class="bi bi-youtube" title="YouTube" style="color: #fff; margin: 0 10px;"></a>
            </div>

        </div>

    </div>

</footer>

<!-- Footer CSS -->

<style>
    .footer {
        background-color: #007bff; /* Matching header background color */
        color: #fff; /* White text for better contrast */
        padding: 10px 0; /* Reduced padding for a smaller footer */
        text-align: center; /* Centers the text */
        border-top: 1px solid #e0e0e0; /* Subtle border for separation */
        font-family: 'Poppins', sans-serif; /* Consistent font style */
    }

    .footer .copyright {
        font-size: 13px; /* Slightly smaller font size */
        margin: 0; /* Reset margin */
    }

    .footer .brand-name {
        color: #fff; /* White color for the brand name */
        font-weight: bold; /* Bold to emphasize the brand */
    }

    .footer .brand-name:hover {
        color: #a288ee; /* Hover effect with a matching light purple color */
        transition: color 0.3s ease; /* Smooth transition for hover effect */
    }

    .footer .social-links a {
        color: #fff; /* White color for icons */
        font-size: 18px; /* Slightly smaller icon size */
        margin: 0 8px; /* Slightly reduced space between icons */
        transition: color 0.3s; /* Smooth transition for hover */
    }

    .footer .social-links a:hover {
        color: #ffc107; /* Change color on hover */
    }

    /* Optional: Add responsive behavior */
    @media (max-width: 768px) {
        .footer .copyright {
            font-size: 12px; /* Smaller text on mobile */
        }
    }
</style>
