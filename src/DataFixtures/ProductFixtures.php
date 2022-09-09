<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        //
        $product1 = new Product();
        $product1->setBrand('apple');
        $product1->setModel('iphone 13 pro');
        $product1->setPrice(1159);
        $product1->setImagePath('https://images.techadvisor.com/cmsdata/slideshow/3677861/iphone_13_pro_review_1.jpg');
        $product1->setDescription('At first glance, the 13 Pro uses a familiar 6.1in Super Retina XDR display but Apple has finally decided to bring ProMotion to the iPhone; with the panel now topping out at a super-smooth 120Hz. The notch is also 20% smaller too. ');
        $manager->persist($product1);
        //2
         $product2 = new Product();
         $product2->setBrand('samsung');
         $product2->setModel('galaxy s22 uitra');
         $product2->setPrice(1259);
         $product2->setImagePath('https://images.techadvisor.com/cmsdata/slideshow/3677861/galaxy_s22_ultra_hands_on_19.jpg');
         $product2->setDescription('The design is distinctly different from the other entries in the Galaxy S22 range (which is to say very ‘Note-like’) and features an integrated S Pen stylus that expands functionality beyond most rivals, especially in terms of productivity.');
         $manager->persist($product2);
          //3
        $product3 = new Product();
        $product3->setBrand('apple');
        $product3->setModel('iphone 13');
        $product3->setPrice(909);
        $product3->setImagePath('https://images.techadvisor.com/cmsdata/slideshow/3677861/iphone_13_review_2.jpg');
        $product3->setDescription('While it loses out on the 13 Pro’s high refresh rate display, the iPhone 13 still boasts cutting-edge features like Apple’s newest 5nm A15 Bionic chip and the latest camera features, like Cinematic Mode.');
        $manager->persist($product3);
         //4
         $product4 = new Product();
         $product4->setBrand('Google');
         $product4->setModel('google pixel 6a');
         $product4->setPrice(459);
         $product4->setImagePath('https://b2c-contenthub.com/wp-content/uploads/2022/07/Google-Pixel-6A_review_1.jpg?resize=1536%2C864&quality=50&strip=all');
         $product4->setDescription('You only get a 60Hz refresh rate screen, slow 18W charging, and a plastic back that we found scratches quite easily. There’s also only a 12.2Mp main camera, the same as on the Pixel 5, rather than the Pixel 6’s 50Mp shooter.');
         $manager->persist($product4);
          //5
        $product5 = new Product();
        $product5->setBrand('google');
        $product5->setModel('google pixel 6 pro');
        $product5->setPrice(899);
        $product5->setImagePath('https://images.techadvisor.com/cmsdata/slideshow/3677861/google_pixel_6_pro_review_29.jpg');
        $product5->setDescription('Despite dropping the ‘Pixel XL’ naming convention, the 6 Pro is an undeniably sizeable phone, with a stunning QHD+ curved-edge OLED display that – for the first time in the series – makes the move to a higher, smoother 120Hz refresh rate (just like the iPhone 13 Pro range).');
        $manager->persist($product5);
         //6
         $product6 = new Product();
         $product6->setBrand('oppo');
         $product6->setModel('find x5 pro');
         $product6->setPrice(1229);
         $product6->setImagePath('https://images.techadvisor.com/cmsdata/slideshow/3677861/oppo_find_x5_pro-05.jpg');
         $product6->setDescription('The Oppo Find X5 Pro is a phenomenal phone by any measure. The 6.7in 10-bit 120Hz QHD+ panel is one of the best displays in any phone right now and Oppo backs it up with 80W wired and 50W wireless charging, a 5000mAh battery, and a top-tier camera that boasts 50Mp sensors on both the main and ultrawide lenses.');
         $manager->persist($product6);
          //7
        $product7 = new Product();
        $product7->setBrand('samsung');
        $product7->setModel('galaxy z flip 4');
        $product7->setPrice(1110);
        $product7->setImagePath('https://b2c-contenthub.com/wp-content/uploads/2022/08/Samsung-Galaxy-Watch-5-_review_9-2.jpg?resize=1536%2C864&quality=50&strip=all');
        $product7->setDescription('Samsung has addressed the Z Flip 3’s issues of bad battery life and middling camera performance for the price and made the Z Flip 4 an all-day phone with the same main and ultrawide cameras as the Galaxy S22 and S22 Plus.');
        $manager->persist($product7);
          //8
        $product8 = new Product();
        $product8->setBrand('vivo');
        $product8->setModel('x80 pro');
        $product8->setPrice(969);
        $product8->setImagePath('https://b2c-contenthub.com/wp-content/uploads/2022/06/vivo_x80_pro_review.jpg?resize=1536%2C864&quality=50&strip=all');
        $product8->setDescription('A Snapdragon 8 Gen 1 chip powers four rear lenses, the main being a wonderfully capable 50Mp sensor. Every lens benefits from Vivo’s V1+ image processing chip, and there’s gimbal stabilisation on the telephoto lens to aid clarity of zoom shots. It’s an incredibly full feature set, and low-light photography is outstanding. Add to that excellent video modes with cinema-wide aspect ratios and a film-like grain and you’ve got a powerhouse camera in your pocket.');
        $manager->persist($product8);
         //9
         $product9 = new Product();
         $product9->setBrand('xiaomi');
         $product9->setModel('xiaomi 12 pro');
         $product9->setPrice(564);
         $product9->setImagePath('https://images.techadvisor.com/cmsdata/slideshow/3677861/xiaomi_12_pro_review_3.jpg');
         $product9->setDescription('It sports a sleek, understated design, there’s a beautiful display paired with a quad-speaker setup and the Snapdragon 8 Gen 1 chip delivers impressive performance. For the most part, the camera system is great too, not quite best-in-class but good.');
         $manager->persist($product9);
          //10
        $product10 = new Product();
        $product10->setBrand('honor');
        $product10->setModel('honor 70');
        $product10->setPrice(599);
        $product10->setImagePath('https://www.hihonor.com/fr/phones/honor-70/buy/?skucode=8133100206002');
        $product10->setDescription('Appareil photo principal
        54 Mpx IMX800 haute sensibilité
        Appareil photo principal
        Ultra Grand Angle et Macro 50 Mpx
        capteur de profondeur de champ 2 Mpx');
        $manager->persist($product10);

        $manager->flush();
    }
}
