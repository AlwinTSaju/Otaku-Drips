const productData = {
    "zoro-limited-edition": {
    title: "Roronoa Zoro v4 Limited Edition",
    category: "One Piece",
    price: 1020,
    originalPrice: 1200,
    images: ["./images/products/roronoa le.png"],
    description: "Channel the spirit of Zoro in this Limited Edition tee. A tribute to strength, loyalty, and legendary swordsmanship."
  },
  "eren-yeager-v4": {
    title: "Attack Titan v4 T Shirt",
    category: "Attack on Titan",
    price: 648,
    originalPrice: 800,
    images: ["./images/products/attack titan.png"],
    description: "Embody the rage of the Attack Titan with this intense v4 tee. Bold, fierce, and unforgettable."
  },
  "luffy-gear-5": {
    title: "Luffy Gear 5 One Piece T-Shirt",
    category: "One Piece",
    price: 850,
    originalPrice: 1000,
    images: ["./images/products/luffy gear 5.png"],
    description: "Unleash your inner pirate with our Luffy Gear 5 Tee. Designed with dynamic artwork from the epic transformation."
  },
  "zoro-v4": {
    title: "Roronoa Zoro v4 One Piece T-Shirt",
    category: "One Piece",
    price: 750,
    originalPrice: 1000,
    images: ["./images/products/zoro v4.png"],
    description: "Step into the shoes of the swordsman with this Zoro V4 tee — intense, iconic, and crafted for warriors."
  },
  "igris-shirt": {
    title: "Igris Solo Leveling Oversized T Shirt",
    category: "Solo Leveling",
    price: 680,
    originalPrice: 800,
    images: ["./images/products/igris.png"],
    description: "Channel the shadows with Igris’ power. This tee brings the Solo Leveling knight's mystique to life."
  },
  "guts-shirt": {
    title: "Guts Berserk Hoodie",
    category: "Berserk",
    price: 680,
    originalPrice: 800,
    images: ["./images/products/guts.png"],
    description: "Feel the fury of Guts in this raw, powerful hoodie. Designed for the brave."
  },
  "eren-shirt": {
    title: "Eren Yeager Attack on Titan T Shirt",
    category: "Attack on Titan",
    price: 680,
    originalPrice: 800,
    images: ["./images/products/eren.png"],
    description: "Wear Eren's resolve. This tee reflects the undying will to fight for freedom."
  },
  "sasuke-shirt": {
    title: "Sasuke Uchiha T Shirt",
    category: "Naruto",
    price: 765,
    originalPrice: 900,
    images: ["./images/products/sasuke.png"],
    description: "Let the power of the Sharingan shine in this sleek Sasuke tee. A classic tribute."
  },
  "black-clover-shirt": {
    title: "Black Clover T Shirt",
    category: "Black Clover",
    price: 680,
    originalPrice: 800,
    images: ["./images/products/black clover.png"],
    description: "Asta’s courage lives in this Black Clover tee. Magic meets determination."
  },
  "bleach-hoodie": {
    title: "Bleach Hoodie",
    category: "Bleach",
    price: 680,
    originalPrice: 800,
    images: ["./images/products/bleach.png"],
    description: "Soul reaper style in every thread. This Bleach hoodie is minimal and powerful."
  },
  "blue-lock-shirt": {
    title: "Blue Lock T Shirt",
    category: "Blue Lock",
    price: 680,
    originalPrice: 800,
    images: ["./images/products/blue lock.png"],
    description: "Ignite your ego with this Blue Lock tee. Designed for goal-seekers and rule-breakers."
  },
  "chainsaw-man-shirt": {
    title: "Chainsaw Man T Shirt",
    category: "Chainsaw Man",
    price: 680,
    originalPrice: 800,
    images: ["./images/products/chainsaw 2.png"],
    description: "Denji’s chaotic spirit packed in this Chainsaw tee. Loud, edgy, and iconic."
  },
  "death-note-shirt": {
    title: "Death Note T Shirt",
    category: "Death Note",
    price: 680,
    originalPrice: 800,
    images: ["./images/products/death note.png"],
    description: "Mystery, intellect, and judgment — this Death Note tee captures it all."
  },
  "gojo-shirt": {
    title: "jujutsu Kaisen T Shirt",
    category: "jujutsu Kaisen",
    price: 680,
    originalPrice: 800,
    images: ["./images/products/gojo.png"],
    description: "Limitless style meets power with this Gojo tee. Vibrant, bold, and dominant."
  },
  "hunter-x-hunter-shirt": {
    title: "Hunter X Hunter T Shirt",
    category: "Hunter X Hunter",
    price: 680,
    originalPrice: 800,
    images: ["./images/products/hunter x hunter.png"],
    description: "Inspired by Gon and Killua, this tee is an adventure waiting to happen."
  },
  "mob-psycho-shirt": {
    title: "Mob Psycho T Shirt",
    category: "Mob Psycho",
    price: 680,
    originalPrice: 800,
    images: ["./images/products/mob.png"],
    description: "Stay calm, stay stylish — this Mob Psycho tee balances simplicity with raw power."
  },
  "nezuko-crop-top": {
    title: "Nezuko Crop Top",
    category: "Demon Slayer",
    price: 680,
    originalPrice: 800,
    images: ["./images/products/nezuko.png"],
    description: "Cute and deadly — the Nezuko Crop Top blends elegance with demon-slaying energy."
  },
  "nine-tails-hoodie": {
    title: "Nine Tails Hoodie",
    category: "Demon Slayer",
    price: 680,
    originalPrice: 800,
    images: ["./images/products/nine tails.png"],
    description: "Embrace mythical firepower in the Nine Tails Hoodie. Fierce and legendary."
  },
  "noragami-shirt": {
    title: "Noragami T Shirt",
    category: "Noragami",
    price: 680,
    originalPrice: 800,
    images: ["./images/products/noragami.png"],
    description: "Stray god style with this Noragami tee. Sleek and inspired by Yato himself."
  },
  "solo-statue-shirt": {
    title: "Solo Leveling Statue T Shirt",
    category: "Solo Leveling",
    price: 680,
    originalPrice: 800,
    images: ["./images/products/solo statue.png"],
    description: "Monumental style from the Solo Leveling universe. This tee is a shadow tribute."
  },
  "chainsaw-man2-shirt": {
    title: "Chainsaw Man Hoodie",
    category: "Chainsaw Man",
    price: 680,
    originalPrice: 800,
    images: ["./images/products/chainsaw man.png"],
    description: "Street fury unleashed. This Chainsaw Man Hoodie is for the bold-hearted."
  },
  "vagabond-shirt": {
    title: "Vagabond T Shirt",
    category: "Vagabond",
    price: 680,
    originalPrice: 800,
    images: ["./images/products/vagabond.png"],
    description: "Miyamoto Musashi’s legacy in one tee. Discipline meets design."
  },
  "zenitsu-nezuko-shirt": {
    title: "Zenitsu & Nezuko T Shirt",
    category: "Demon Slayer",
    price: 680,
    originalPrice: 800,
    images: ["./images/products/zenitsu nezuko.png"],
    description: "Harmony and courage in one frame. This tee is a tribute to the quiet strength of Zenitsu and Nezuko."
  }
};

export default productData;