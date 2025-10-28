<?php

// @formatter:off
// phpcs:ignoreFile
/**
 * A helper file for your Eloquent Models
 * Copy the phpDocs from this file to the correct Model,
 * And remove them from this file, to prevent double declarations.
 *
 * @author Barry vd. Heuvel <barryvdh@gmail.com>
 */


namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $description
 * @property int $is_active
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereIsActive($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Category whereUpdatedAt($value)
 */
	class Category extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $description
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Location|null $location
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Phone> $phones
 * @property-read int|null $phones_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumer whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Consumer whereUserId($value)
 */
	class Consumer extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $name
 * @property string|null $region
 * @property string|null $members
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Location|null $location
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Phone> $phones
 * @property-read int|null $phones_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereMembers($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereRegion($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Cooperative whereUserId($value)
 */
	class Cooperative extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $order_id
 * @property int|null $transporter_id
 * @property \Illuminate\Support\Carbon|null $estimated_delivery_date
 * @property string $status
 * @property string|null $signature_path
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\User|null $transporter
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Delivery newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Delivery newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Delivery query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Delivery whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Delivery whereEstimatedDeliveryDate($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Delivery whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Delivery whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Delivery whereSignaturePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Delivery whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Delivery whereTransporterId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Delivery whereUpdatedAt($value)
 */
	class Delivery extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $fileable
 * @property-read mixed $url
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|File withoutTrashed()
 */
	class File extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $product_id
 * @property int $quantity
 * @property string $reason
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryEntry newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryEntry newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryEntry query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryEntry whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryEntry whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryEntry whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryEntry whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryEntry whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryEntry whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryEntry whereUserId($value)
 */
	class InventoryEntry extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $product_id
 * @property int|null $order_item_id
 * @property int $quantity
 * @property string $reason
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\OrderItem|null $orderItem
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryExit newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryExit newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryExit query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryExit whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryExit whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryExit whereOrderItemId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryExit whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryExit whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryExit whereReason($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryExit whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|InventoryExit whereUserId($value)
 */
	class InventoryExit extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $street
 * @property string|null $postal_code
 * @property string|null $interior_number
 * @property string|null $exterior_number
 * @property string|null $longitude
 * @property string|null $latitude
 * @property string $locationable_type
 * @property int $locationable_id
 * @property int|null $neighborhood_id
 * @property int|null $municipality_id
 * @property int|null $state_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $locationable
 * @property-read \App\Models\Neighborhood|null $neighborhood
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereExteriorNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereInteriorNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereLatitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereLocationableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereLocationableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereLongitude($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereMunicipalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereNeighborhoodId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereStateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereStreet($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Location withoutTrashed()
 */
	class Location extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $description
 * @property string $key
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereKey($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Module withoutTrashed()
 */
	class Module extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $code
 * @property int $state_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Neighborhood> $neighborhoods
 * @property-read int|null $neighborhoods_count
 * @property-read \App\Models\State $state
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereStateId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Municipality whereUpdatedAt($value)
 */
	class Municipality extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $name
 * @property string|null $postal_code
 * @property int $municipality_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Municipality $municipality
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Neighborhood newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Neighborhood newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Neighborhood query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Neighborhood whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Neighborhood whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Neighborhood whereMunicipalityId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Neighborhood whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Neighborhood wherePostalCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Neighborhood whereUpdatedAt($value)
 */
	class Neighborhood extends \Eloquent {}
}

namespace App\Models{
/**
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Notification query()
 */
	class Notification extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $consumer_id
 * @property int|null $cooperative_id
 * @property numeric $total_amount
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\User|null $consumer
 * @property-read \App\Models\User|null $cooperative
 * @property-read \App\Models\Delivery|null $delivery
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $orderItems
 * @property-read int|null $order_items_count
 * @property-read \App\Models\Payment|null $payment
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereConsumerId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCooperativeId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereTotalAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Order whereUpdatedAt($value)
 */
	class Order extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $order_id
 * @property int $product_id
 * @property int $quantity
 * @property string $price
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order $order
 * @property-read \App\Models\Product $product
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereProductId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|OrderItem whereUpdatedAt($value)
 */
	class OrderItem extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int $order_id
 * @property numeric $amount
 * @property string $payment_method
 * @property string $status
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Order $order
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereAmount($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereOrderId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment wherePaymentMethod($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereStatus($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Payment whereUpdatedAt($value)
 */
	class Payment extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $number
 * @property string|null $dial_code
 * @property string|null $type
 * @property string $phoneable_type
 * @property int $phoneable_id
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $phoneable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Phone newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Phone newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Phone onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Phone query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Phone whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Phone whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Phone whereDialCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Phone whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Phone whereNumber($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Phone wherePhoneableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Phone wherePhoneableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Phone whereType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Phone whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Phone withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Phone withoutTrashed()
 */
	class Phone extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $title
 * @property string|null $description
 * @property \App\Enums\PhotoUsage $usage
 * @property string $filename
 * @property string $path
 * @property string|null $mime_type
 * @property int|null $size
 * @property string $photoable_type
 * @property int $photoable_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read mixed $url
 * @property-read \Illuminate\Database\Eloquent\Model|\Eloquent $photoable
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Photo newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Photo newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Photo query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Photo whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Photo whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Photo whereFilename($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Photo whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Photo whereMimeType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Photo wherePath($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Photo wherePhotoableId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Photo wherePhotoableType($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Photo whereSize($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Photo whereTitle($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Photo whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Photo whereUsage($value)
 */
	class Photo extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string|null $certification
 * @property int|null $user_id
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\Location|null $location
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Phone> $phones
 * @property-read int|null $phones_count
 * @property-read \App\Models\User|null $user
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producer newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producer newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producer query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producer whereCertification($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producer whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producer whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producer whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Producer whereUserId($value)
 */
	class Producer extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property int|null $user_id
 * @property int|null $category_id
 * @property string $name
 * @property string $description
 * @property numeric $price
 * @property int $stock_quantity
 * @property bool $is_available
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Category|null $category
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\OrderItem> $orderItems
 * @property-read int|null $order_items_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Photo> $photos
 * @property-read int|null $photos_count
 * @property-read \App\Models\User|null $producer
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCategoryId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereDescription($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereIsAvailable($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product wherePrice($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereStockQuantity($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product whereUserId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|Product withoutTrashed()
 */
	class Product extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string|null $code
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Municipality> $municipalities
 * @property-read int|null $municipalities_count
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State whereCode($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|State whereUpdatedAt($value)
 */
	class State extends \Eloquent {}
}

namespace App\Models{
/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property \Illuminate\Support\Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property \Illuminate\Support\Carbon|null $deleted_at
 * @property-read \App\Models\Consumer|null $consumer
 * @property-read \App\Models\Cooperative|null $cooperative
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \App\Models\Notification> $notifications
 * @property-read int|null $notifications_count
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Permission> $permissions
 * @property-read int|null $permissions_count
 * @property-read \App\Models\Producer|null $producer
 * @property-read \Illuminate\Database\Eloquent\Collection<int, \Spatie\Permission\Models\Role> $roles
 * @property-read int|null $roles_count
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newModelQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User newQuery()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User onlyTrashed()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User permission($permissions, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User query()
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User role($roles, $guard = null, $without = false)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereCreatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereDeletedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmail($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereEmailVerifiedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereId($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereName($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User wherePassword($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereRememberToken($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User whereUpdatedAt($value)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withTrashed(bool $withTrashed = true)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutPermission($permissions)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutRole($roles, $guard = null)
 * @method static \Illuminate\Database\Eloquent\Builder<static>|User withoutTrashed()
 */
	class User extends \Eloquent {}
}

