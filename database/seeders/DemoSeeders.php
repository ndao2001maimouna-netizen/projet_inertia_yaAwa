<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use RuntimeException;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class DemoSeeders extends Seeder
{
  

    private array $donnees;

    public function lireDemo():array{
        $chemin = database_path('data/demo.json');

        if(!file_exists($chemin)){
            throw new RuntimeException("Fichier de démo introuvable : {$chemin}");
        }

        $contenue = file_get_contents($chemin);

        return json_decode($contenue,true,512,JSON_THROW_ON_ERROR);
    }

      /**
     * Run the database seeds.
     */

    public function run(): void
    {
        $this->donnees = $this->lireDemo();
        DB::transaction(function(){
            $this->creerUtilisateur();
            $this->insererCategories();
            $this->insererProducts();
            $this->insererZonesLivraison();
            $this->insererPromotions();
            $this->insererCommandes();
        });

        
    }

    private function creerUtilisateur():void{
        $email = config('demo.admin.email');
        $password = config('demo.admin.password');

        if(blank($email) || blank($password)){
            throw new RuntimeException('Renseignez DEMO_ADMIN_EMAIL et DEMO_ADMIN_PASSWORD dans le fichier .env.');
        }

        User::factory()->create([
            'name' => config('demo.admin.name'),
            'email' => $email,
            'password' => $password,
            'is_admin' => true,
        ]);
    }

    private function insererCategories():void{
        foreach ($this->donnees['categories'] as $categorie) {
            DB::table('categories')->insert([
                'id'=>(int)$categorie['id'],
                'name'=>$categorie['name'],
                'color'=>$categorie['color'],
                'image'=>$categorie['image']
            ]);
        }
    }

    private function insererProducts(): void
    {
        foreach ($this->donnees['products'] as $produit) {
            DB::table('products')->insert([
                'id' => (int) $produit['id'],
                'name' => $produit['name'],
                'category_id' => (int) $produit['categoryId'],
                'price' => $produit['price'],
                'sale_price' => $produit['salePrice'],
                'stock' => $produit['stock'],
                'unit' => $produit['unit'],
                'origin' => $produit['origin'],
                'description' => $produit['description'],
                'image' => $produit['image'],
                'active' => $produit['active'],
                'featured' => $produit['featured'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function insererZonesLivraison(): void
    {
        foreach ($this->donnees['deliveryZones'] as $zone) {
            DB::table('delivery_zones')->insert([
                'id' => (int) $zone['id'],
                'name' => $zone['name'],
                'fee' => $zone['fee'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function insererPromotions(): void
    {
        foreach ($this->donnees['promotions'] as $promotion) {
            $expiration = $promotion['expiresAt'] !== null
                ? Carbon::parse($promotion['expiresAt'])
                : null;

            DB::table('promotions')->insert([
                'id' => (int) $promotion['id'],
                'code_promo' => $promotion['code'],
                'percentage' => $promotion['percentage'],
                'active' => $promotion['active'],
                'expires_at' => $expiration,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    private function insererCommandes(): void
    {
        foreach ($this->donnees['orders'] as $commande) {
            $client = $commande['customer'];
            $remise = $commande['discount'];

            $creeLe = Carbon::parse($commande['createdAt']);
            $modifieLe = Carbon::parse($commande['updatedAt']);

            $idCommande = DB::table('orders')->insertGetId([
                'reference' => $commande['reference'],
                'checkout_token' => (string) Str::uuid(),
                'customer_name' => $client['fullName'],
                'customer_phone' => $client['phone'],
                'delivery_zone_id' => (int) $client['zoneId'],
                'zone_name' => $client['zoneName'],
                'adresse' => $client['address'],
                'fulfillmentMode' => $commande['fulfillmentMode'],
                'note' => $commande['note'],
                'sub_total' => $commande['subtotal'],
                'discount_percentage' => $remise['percentage'],
                'discount_amount' => $remise['amount'],
                'delivery_fee' => $commande['deliveryFee'],
                'total' => $commande['total'],
                'statut' => $commande['status'],
                'stock_applied' => $commande['stockApplied'],
                'stock_restored' => $commande['stockRestored'],
                'canceled_at' => $commande['status'] === 'ANNULEE' ? $modifieLe : null,
                'created_at' => $creeLe,
                'updated_at' => $modifieLe,
            ]);

            foreach ($commande['items'] as $article) {
                DB::table('orders_items')->insert([
                    'order_id' => $idCommande,
                    'product_id' => (int) $article['productId'],
                    'name' => $article['name'],
                    'unit' => $article['unit'],
                    'unit_price' => $article['unitPrice'],
                    'quantity' => $article['quantity'],
                    'line_total' => $article['lineTotal'],
                    'created_at' => $creeLe,
                    'updated_at' => $creeLe,
                ]);
            }
        }
    }
}