<?php
/**
 * The template for displaying the footer.
 *
 * Contains the closing of the #content div and all content after
 *
 * @package Sydney
 */
?>

	<?php do_action('sydney_before_footer'); ?>


<div id='fb-root'></div>

    <footer id="colophon" class="site-footer" role="contentinfo" itemscope itemtype="https://schema.org/WPFooter">
	<a rel="nofollow" href="https://wa.me/40747668204" itemprop="sameAs" aria-label="Whatsapp link"><?php echo simonamarin_icon( 'whatsapp', 'whatsapp-footer' ); ?></a>
      <div class="container" itemprop="publisher" itemscope itemtype="https://schema.org/Organization">
        <div class="row">
            <div class="col-md-12 col-xs-12">
                <div class="text8 fb header-links">
                    <span class="textTS"><a href="https://simonamarin.ro/" rel="home" title="Pagina principală a Cabinetului Individual de Psihologie" itemprop="url">Cabinet Individual de Psihologie</a></span>
                    <a rel="nofollow" href="https://www.facebook.com/PsihologSimonaMarin" title="Pagina de Facebook" aria-label="Facebook" itemprop="sameAs"><?php echo simonamarin_icon( 'facebook' ); ?></a>
                    <a rel="nofollow" href="https://wa.me/40747668204" title="Conectează-te pe WhatsApp" aria-label="WhatsApp" itemprop="sameAs"><?php echo simonamarin_icon( 'whatsapp' ); ?></a>
                    <a rel="nofollow" href="https://www.instagram.com/simonamarin.ro/" title="Urmărește-mă pe Instagram" aria-label="Instagram" itemprop="sameAs"><?php echo simonamarin_icon( 'instagram' ); ?></a>
                    <a href="tel:0747668204" title="Sună-mă la 0747668204" aria-label="Telefon" itemprop="telephone"><?php echo simonamarin_icon( 'phone' ); ?></a>
                    <a href="mailto:psihologsimonamarin@gmail.com" title="Trimite-mi un email la psihologsimonamarin@gmail.com" aria-label="Email" itemprop="email"><?php echo simonamarin_icon( 'envelope' ); ?></a>
                </div>
<div class="row">
            </div>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-md-4 col-xs-12">
                <div class="row">
                    <div class="text8 col-md-12" itemprop="openingHoursSpecification" itemscope itemtype="https://schema.org/OpeningHoursSpecification">
                        Program:<br>
                        <span itemprop="dayOfWeek">Luni-Vineri</span>: <span itemprop="opens">10:00</span> - <span itemprop="closes">19:00</span><br>
                        <span itemprop="dayOfWeek">Sambata</span>: <span itemprop="opens">10:00</span> - <span itemprop="closes">15:00</span>
                    </div>
                </div>
            </div>
            <div class="col-md-4 col-xs-12">
                <a class="col-md-12" href="https://simonamarin.ro/servicii-psihologice/">Servicii Psihologice</a>
                <a class="col-md-12" class="text8 _ps2id" href="https://simonamarin.ro/servicii-psihologice/" aria-label="Servicii">Psihologie clinica</a>
                <a class="col-md-12" class="text8 _ps2id" href="https://simonamarin.ro/servicii-psihologice/"  aria-label="Servicii">Psihonutritie clinica</a>

            </div>
            <div class="col-md-4 col-xs-12">
                <a class="col-md-12" class="text8 _ps2id" href="https://simonamarin.ro/servicii-psihologice/"  aria-label="Servicii">Consiliere psihologica</a>
                <a class="col-md-12" class="text8 _ps2id" href="https://simonamarin.ro/servicii-psihologice/" aria-label="Servicii">Servicii corporate</a>
                <a class="col-md-12" class="text8 _ps2id" href="https://simonamarin.ro/servicii-psihologice/"  aria-label="Servicii">Consiliere vocationala</a>
</div>
        </div>
            <div class="col-md-12 col-xs-12">
            <div class="row">

        </div>
    </div>
    </div>
    <div class="col-md-12 col-xs-12 copyright" itemprop="copyrightHolder" style="margin-top:40px;">
            <p>Toate drepturile rezervate &copy; <span itemprop="name">Cabinet Individual de Psihologie Simona Marin</span></p>
            <a href="https://simonamarin.ro/termeni-si-conditii/" title="Termeni și condiții">Terms and Conditions</a>
            <img width="76" height="26" class="ls-is-cached lazyloaded" src="https://simonamarin.ro/wp-content/uploads/2019/01/comodo_secure_seal_76x26_transp.png" data-src="https://simonamarin.ro/wp-content/uploads/2019/01/comodo_secure_seal_76x26_transp.png" alt="comodo_secure_seal">
    </div>

</footer>

	<?php do_action('sydney_after_footer'); ?>

</div><!-- #page -->

<?php wp_footer(); ?>

</body>
</html>