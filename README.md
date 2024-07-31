PHP ORM Kütüphanesi

PHP ile yazılmış bu ORM (Object-Relational Mapping) kütüphanesi, veritabanı etkileşimlerini basitleştirir ve veritabanı bağlantılarını, sorgu oluşturmayı, sonuç işleme ve model yönetimini sağlayan bir dizi sınıf sunar.

Özellikler

- Veritabanı Bağlantısı: Veritabanı bağlantılarını kolayca yönetir ve yapılandırır.
- Sorgu Oluşturucu: SQL sorgularını dinamik olarak oluşturur ve çalıştırır, join, filtreleme, sıralama ve sayfalama desteği sağlar.
- Koleksiyon İşleme: Sorgu sonuçlarını işleyip manipüle etmek için filtreleme, dönüştürme ve toplama yöntemleri sunar.
- Model Yönetimi: Kayıt oluşturma, okuma, güncelleme ve silme işlemlerini basit bir şekilde yapar.

Kurulum

Bu kütüphaneyi kullanmak için gerekli dosyaları projenize dahil edin:

require_once 'DatabaseConnection.php';
require_once 'QueryBuilder.php';
require_once 'Collection.php';
require_once 'Model.php';

Kullanım

1. Veritabanı Bağlantısı

DatabaseConnection sınıfını kullanarak veritabanı bağlantısını alabilirsiniz.

use ComplexORM\DatabaseConnection;

$pdo = DatabaseConnection::getConnection();

2. Sorgu Oluşturucu

QueryBuilder sınıfını kullanarak SQL sorguları oluşturabilirsiniz.

- table(string $table): Veritabanı tablosunu belirtir.

queryBuilder = new QueryBuilder($pdo);
queryBuilder->table('users');

- select(string $columns): Sorguda seçilecek sütunları belirtir.

queryBuilder->select('name, email');

- where(string $column, string $operator, $value): WHERE koşulu ekler.

queryBuilder->where('age', '>', 25);

- join(string $table, string $first, string $operator, string $second): JOIN işlemi yapar.

queryBuilder->join('profiles', 'users.id', '=', 'profiles.user_id');

- orderBy(string $column, string $direction = 'ASC'): Sorgu sonuçlarını sıralar.

queryBuilder->orderBy('age', 'DESC');

- limit(int $limit): Sonuçların maksimum sayısını belirtir.

queryBuilder->limit(10);

- offset(int $offset): Sonuçları belirtilen kayıttan itibaren almaya başlar.

queryBuilder->offset(20);

- groupBy(string $column): Sonuçları belirtilen sütuna göre gruplar.

queryBuilder->groupBy('age');

- having(string $column, string $operator, $value): GROUP BY ile HAVING koşulu ekler.

queryBuilder->having('COUNT(id)', '>', 1);

- union(QueryBuilder $queryBuilder): Başka bir sorgu ile UNION yapar.

$secondQuery = (new QueryBuilder($pdo))
    ->table('users')
    ->select('name')
    ->where('age', '<', 30);

queryBuilder->union($secondQuery);

- get(): Oluşturulan sorguyu çalıştırır ve sonuçları döner.

$results = queryBuilder->get();
print_r($results);

3. Koleksiyon İşleme

Collection sınıfını kullanarak sorgu sonuçlarını işleyebilirsiniz.

- each(callable $callback): Koleksiyon üzerindeki her bir öğe için callback fonksiyonunu çalıştırır.

use ComplexORM\Collection;

$usersCollection = new Collection($users);
$usersCollection->each(function($user) {
    echo "User: {$user['name']}, Email: {$user['email']}\n";
});

- map(callable $callback): Koleksiyon üzerindeki her bir öğeyi callback fonksiyonuna göre dönüştürür.

$mappedUsers = $usersCollection->map(function($user) {
    return [
        'name' => $user['name'],
        'email' => $user['email']
    ];
});
print_r($mappedUsers->toArray());

- filter(callable $callback): Koleksiyonun öğelerini belirli bir koşula göre filtreler.

$filteredUsers = $usersCollection->filter(function($user) {
    return $user['age'] > 25;
});
print_r($filteredUsers->toArray());

- reduce(callable $callback, $initial = null): Koleksiyon öğelerini tek bir değere indirger.

$totalSalaries = $usersCollection->reduce(function($carry, $user) {
    return $carry + ($user['salary'] ?? 0);
}, 0);
echo "Total Salaries: $totalSalaries\n";

- first(callable $callback = null): Koleksiyonun ilk öğesini döner.

$firstUser = $usersCollection->first();
print_r($firstUser);

- last(callable $callback = null): Koleksiyonun son öğesini döner.

$lastUser = $usersCollection->last();
print_r($lastUser);

- toArray(): Koleksiyondaki öğeleri bir diziye dönüştürür.

$usersArray = $usersCollection->toArray();
print_r($usersArray);

4. Model Yönetimi

Model sınıfını kullanarak veritabanı kayıtlarını yönetebilirsiniz.

- query(): QueryBuilder örneğini döner.

use ComplexORM\User;

$queryBuilder = User::query();

- create(array $attributes): Yeni bir model örneği oluşturur ve veritabanına kaydeder.

$user = User::create([
    'name' => 'John Doe',
    'email' => 'john.doe@example.com',
    'age' => 29,
    'salary' => 6000.00
]);

- find($id): Belirtilen ID'ye sahip olan kayıtı bulur.

$user = User::find(1);
print_r($user);

- all(): Tablo üzerindeki tüm kayıtları döner.

$users = User::all();
print_r($users->toArray());

- count(): Tablo üzerindeki kayıt sayısını döner.

$count = User::count();
echo "User Count: $count\n";

- sum($column): Belirtilen sütundaki değerlerin toplamını döner.

$totalSalary = User::sum('salary');
echo "Total Salary: $totalSalary\n";

- max($column): Belirtilen sütundaki en yüksek değeri döner.

$maxAge = User::max('age');
echo "Maximum Age: $maxAge\n";

- min($column): Belirtilen sütundaki en düşük değeri döner.

$minAge = User::min('age');
echo "Minimum Age: $minAge\n";

- avg($column): Belirtilen sütundaki değerlerin ortalamasını döner.

$avgAge = User::avg('age');
echo "Average Age: $avgAge\n";

- save(): Modeli veritabanına kaydeder.

$user = new User();
$user->name = 'Jane Doe';
$user->email = 'jane.doe@example.com';
$user->age = 25;
$user->salary = 5500.00;
$user->save();

İletişim ve destek için https://instagram.com/efozdemirx
