<?php

namespace Database\Factories;

use App\Models\Brand;
use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->randomElement($this->products())['title'],
            'price' => fake()->randomFloat(2, 10, 100),
            'description' => fake()->text(),
            'category_uuid' => Category::inRandomOrder()->value('uuid'),
            'metadata' => [
                'image' => fake()->uuid(),
                'brand' => Brand::inRandomOrder()->value('uuid'),
            ],
        ];
    }

    protected function products(): array
    {
        return [
            [
                "title" => "Shout for Pets Odor and Urine Eliminator - Effective Way to Remove Puppy & Dog Odors and Stains from Carpets & Rugs",
                "uuid" => "41c622c5-b4c0-3e17-9bb5-c2fbc178b25f",
            ],
            [
                "title" => "Amazon Basics Dog and Puppy Pads, Leak-proof 5-Layer Pee Pads with Quick-dry Surface for Potty Training",
                "uuid" => "6e214819-fee1-31be-bc91-e5505d16401a",
            ],
            [
                "title" => "Fresh Wave Odor Removing Gel, 15 oz. (Pack of 2)",
                "uuid" => "319df9b1-4d22-3a14-b9a0-d4adb118f560",
            ],
            [
                "title" => "Nature’s Miracle Cage Cleaner 24 fl oz, Small Animal Formula, Cleans And Deodorizes Small Animal Cages, 2nd Edition",
                "uuid" => "2ff620b8-c5c6-37d0-91d9-3224ade24dac",
            ],
            [
                "title" => "Black Diamond Stoneworks Ur-in Control Eliminates Urine Odors – Removes Cat, Dog, Pet, Odors Human Smells from Carpet",
                "uuid" => "5249910d-7027-3dc3-af2c-cdf80ec69047",
            ],
            [
                "title" => "PetSafe ScoopFree Cat Litter Crystal Tray Refills for ScoopFree Self-Cleaning Cat Litter Boxes - 3-Pack - Non-Clumping, Less Mess",
                "uuid" => "6cd3371b-80cf-3633-b4b1-9b9dec90f82f",
            ],
            [
                "title" => "Purina Tidy Cats Clumping Cat Litter",
                "uuid" => "7babd249-6a95-300c-a37e-45d2020b36e0",
            ],
            [
                "title" => "ARM & Hammer Clump & Seal Platinum Cat Litter, Multi-Cat, 40 lb",
                "uuid" => "8e30d2d5-5c0a-3567-a70f-e1973a5746c2",
            ],
            [
                "title" => "Precious Cat Unscented Ultra Clumping Cat Litter",
                "uuid" => "2287c0b8-e746-3ac8-b61a-cd2cf1c988ed",
            ],
            [
                "title" => "Fresh Step Scented Litter with The Power of Febreze, Clumping Cat Litter",
                "uuid" => "10d76e0c-f071-34c8-95d2-ba6b89a2656c",
            ],
            [
                "title" => "Purina Fancy Feast Gravy Wet Cat Food Variety Pack, Seafood Grilled Collection - (24) 3 oz. Cans",
                "uuid" => "8c03a2a4-8b99-3499-a85a-399aa30ae6c8",
            ],
            [
                "title" => "Purina Beneful Wet Dog Food Variety Pack",
                "uuid" => "aa8adb84-a19e-3a1a-bd4f-b8017eb2b458",
            ],
            [
                "title" => "Natures Recipe Wet Dog Food in Savory Broth, 2.75 Ounce Cups",
                "uuid" => "c9b1f01f-7826-3cc8-bd46-2343e0ae43c0",
            ],
            [
                "title" => "Purina Friskies Gravy Wet Cat Food Variety Pack, Poultry Shreds, Meaty Bits & Prime Filets - (32) 5.5 oz.",
                "uuid" => "bdff7712-9797-3084-8af8-7d5bc9fc11c3",
            ],
            [
                "title" => "Cesar 36 count & 60 count Variety Pack Soft Wet Dog Food",
                "uuid" => "199c0c6b-fba8-3692-9a4f-4e2d7a9f5fb6",
            ],
            [
                "title" => "Newday 50 Pieces Double-Headed Dog Cat Pet Toothbrush , Super Soft Bristles Oral Care Teeth , pet tooth brush for dogs , dog toothbrushes",
                "uuid" => "6ff41f00-dccc-35df-8863-31519e964f5a",
            ],
            [
                "title" => "Dolzzeiy Dog Dental Care Kit Pet Oral Care Kit. One Pack-Beef Flavor Toothpaste with Long Toothbrush and Finger Toothbrush",
                "uuid" => "b689ff59-60ec-3fa4-98bb-552075554486",
            ],
            [
                "title" => "Arm & Hammer for Pets Tartar Control Kit for Dogs-Contains Toothpaste, Dog Toothbrush & Fingerbrush",
                "uuid" => "54ecabd7-4bbd-32c1-922d-a6f95e77ff79",
            ],
            [
                "title" => "Petkin Plaque Toothwipes, Fresh Mint Wipes - Natural Formula Cleans Teeth, Gums & Freshens Breath - Convenient, & Easy to Use Oral Care",
                "uuid" => "fe692b85-e8cf-3177-a380-eab52f9f0a7b",
            ],
            [
                "title" => "Nylabone Advanced Oral Care Water Additives for Dogs",
                "uuid" => "a2b91493-d0db-3666-a040-b3f18350ddde",
            ],
            [
                "title" => "Zesty Paws Allergy Immune Supplement for Dogs - with Omega 3 Wild Alaskan Salmon Fish Oil & EpiCor + Digestive Prebiotics",
                "uuid" => "5d3adf65-ee50-324e-a43b-17eeaeaa358d",
            ],
            [
                "title" => "TevraPet FirstAct Plus Cat Flea and Tick Treatment, Flea Medicine for Cats 1.5 lbs and up, 6 Months Prevention",
                "uuid" => "2ecab778-5747-3897-baaf-aa4131811d21",
            ],
            [
                "title" => "8in1 Safe-Guard Canine Dewormer for Small Dogs, 3 Day Treatment",
                "uuid" => "863c558f-0598-31b7-a669-3f013f362538",
            ],
            [
                "title" => "Nutramax Laboratories Dasuquin with MSM Soft Chews",
                "uuid" => "a9428462-b9f4-3043-bb04-1d9ff7848010",
            ],
            [
                "title" => "Joint Supplement for Dogs - Green Lipped Mussel, MSM + Glucosamine Formula - Helps to Restore Mobility, Relieve Arthritis & Hip Dysplasia",
                "uuid" => "c313ad90-1a98-3f4e-83e8-f451a4e0afef",
            ],
            [
                "title" => "Fish Oil with Vitamin E, Cat and Dog Vitamin for Improved Pet Wellbeing, Joint Health Supplement with Fish Oils, Omega Fatty Acid Supplement",
                "uuid" => "3658a8fb-b2eb-3b99-80fd-6d2ab75beb00",
            ],
            [
                "title" => "Rx Vitamins Amino B Plex for Pets - B Vitamin Complex Plus Amino Acids for Dogs & Cats - Vitamin Supplements for Dogs & Cats Total",
                "uuid" => "914c63ad-dc55-354f-a517-07b8dd1fee41",
            ],
            [
                "title" => "Vetflix Natural Pet Supplement for Dogs and Cats - Immune System Support and Overall Wellbeing",
                "uuid" => "9833ef05-f759-3d0f-823c-8a9299a161e5",
            ],
            [
                "title" => "ProSense Vitamin Solutions 90 Count, Chewable Tablets for Dogs, Helps Support Overall Wellness",
                "uuid" => "7469b4c8-b527-3d79-9f37-e23f9942569c",
            ],
            [
                "title" => "Wholistic Pet Organics Canine Complete: Dog Multivitamin for Total Body Health - Dog Supplement with Vitamins, Minerals, Prebiotics",
                "uuid" => "5c60568f-6d45-3842-814a-1afafb5562c6",
            ],
            [
                "title" => "Aquapaw Dog Bath Brush - Sprayer and Scrubber Tool in One - Indoor/Outdoor Dog Bathing Supplies - Pet Grooming",
                "uuid" => "7a05a81a-b6a2-3d82-8c2c-3421133652da",
            ],
            [
                "title" => "BAOSADI Pet Dog Grooming Hammock Harness for Cats & Dogs,10 in 1 Dog Holder for Grooming",
                "uuid" => "142921f5-6056-3af7-8332-670ea17b227e",
            ],
            [
                "title" => "SHELANDY Pet Grooming arm with clamp for Large and Small Dogs - 35 inch Height Adjustable and Free Two No Sit Haunch Holder",
                "uuid" => "417ebc9d-3c2b-3922-a532-42ef0025dc13",
            ],
            [
                "title" => "oneisall Dog Shaver Clippers Low Noise Rechargeable Cordless Electric Quiet Hair Clippers Set for Dogs Cats Pets",
                "uuid" => "5f54f564-d5c9-3618-a263-6586c9a4fc2f",
            ],
            [
                "title" => "VEVOR Dog Grooming Tub, 38\" L Pet Wash Station, Professional Stainless Steel Pet Grooming Tub Rated 180LBS Load Capacity",
                "uuid" => "5b63cf7a-2bb9-3bcc-8400-6c0899c1efd9",
            ],
            [
                "title" => "Seresto Flea and Tick Collar for Dogs, 8-Month Flea and Tick Collar for Large Dogs Over 18 Pounds",
                "uuid" => "07e6d263-165e-31d5-9877-b605ab20a23d",
            ],
            [
                "title" => "FRONTLINE Plus Flea and Tick Treatment for Cats",
                "uuid" => "8c75d88d-dfc2-3fba-8793-5298b729495b",
            ],
            [
                "title" => "Advantage II Flea Treatment and Prevention for Small Cats",
                "uuid" => "dc3ab881-b540-3959-a91b-8d6b08688ed7",
            ],
            [
                "title" => "FRONTLINE Plus Flea and Tick Treatment for Dogs (Small Dog, 5-22 Pounds)",
                "uuid" => "77e71f6c-8046-35cb-bdea-c8a9037c39b6",
            ],
            [
                "title" => "K9 Advantix II Flea and Tick Prevention for Dogs",
                "uuid" => "7d9525ad-590c-3604-92c9-25c64c973b3d",
            ],
            [
                "title" => "Only Natural Pet RawNibs Freeze Dried Food",
                "uuid" => "f1f32404-3e2e-3d45-89fa-865c9e2e63a4",
            ],
            [
                "title" => "Kaytee Food from The Wild Natural Snack",
                "uuid" => "32b646e4-250a-37b6-8116-e263ea9f060d",
            ],
            [
                "title" => "Wellness Natural Pet Food Treat",
                "uuid" => "da4a3235-42d0-3ac8-a44f-cefa187f773e",
            ],
            [
                "title" => "SmartBones Stuffed Twistz with Peanut Butter 35 Twistz Total, Rawhide-Free Chews for Dogs Stuffed with Pork Flavor",
                "uuid" => "c9ce3c51-0ed2-3332-89d2-4f43edadd855",
            ],
            [
                "title" => "Pet’s Choice Naturals Bully Spirals, Low Odor Bully Stick Chew Treat for Dogs, 9\" 2 ct",
                "uuid" => "89656c1c-8c0a-353f-84ea-1d801cca680c",
            ],
            [
                "title" => "Royal Canin Small Breed Adult Dry Dog Food",
                "uuid" => "b0e36254-83c7-3a5d-911e-1c20d72a9d88",
            ],
            [
                "title" => "Purina ONE SmartBlend Natural Adult Chicken & Rice Dry Dog Food",
                "uuid" => "5c119d9e-4dfe-341a-880a-2ab9e1ac959d",
            ],
            [
                "title" => "Hills Science Diet Dry Dog Food, Adult, Sensitive Stomach & Skin Recipes",
                "uuid" => "d02fbe7f-5cf7-30aa-8159-16e8ea086d96",
            ],
            [
                "title" => "Pedigree Adult Dry Dog Food, Chicken Flavor, All Bag Sizes",
                "uuid" => "bba1e41e-4f10-3305-b5f9-56d2680277ff",
            ],
            [
                "title" => "Blue Buffalo Life Protection Formula Natural Adult Dry Dog Food",
                "uuid" => "a96f0936-77d7-3313-b1a4-69295167905c",
            ],
        ];
    }
}
