# Resources

Simple helper classes for working with resource collections and items

## Collections and Items

The base collections class extends Laravels collection class, which gives us a number of useful methods. Check out the Laravel docs to see exactly what is available.

A new ability is to create each item of the collection as a new instance of an item class. By default this is the base item class inside this package, but you can always override it by defining your custom collections and items, and them simply extending the ones from this package.

An example would be a collection of events, so we would create a collection class:

**App\Resources\Collections\EventCollection**

    <?php namespace App\Resources\Collections;
    
    class EventCollection extends \Creolab\Resources\Collection {
        
        protected $item = '\App\Resources\Items\EventItem';
        
    }

And the item class:

**App\Resources\Items\EventItem**

    <?php namespace App\Resources\Items;
    
    class EventItem extends \Creolab\Resources\Item {
    
    }

So now every time we create a new user collection it's created as a collection of **UserItem** instances.

In your item class you can then create whatever logic you like, as it actually behaves like some sort of a presenter.
You can also use custom attributes as with Eloquent models. So if you have a UserItem class, with the attributes **first_name** and **last_name**, you can create a method called **getFullNameAttribute** in your **UserItem** class, and the concatenate the first and last name, and then in your view/response call something like this:

    {{ $user->full_name }}

## Data Transformation

Since I almost exclusively create repositories for my project, I miss some of the thing available from Eloquent models, and this is exactly why this package was created. Let's say we have a EventItem like before, which has a couple of attributes that would be more useful as objects. This works very good for relations.

So our item class would now look like this:

**App\Resources\Items\EventItem**

    <?php namespace App\Resources\Items;
    
    class EventItem extends \Creolab\Resources\Item {
    
        protected $transform = array(
            'date'      => 'date',
            'from'      => 'datetime',
            'to'        => 'datetime',
            'author'    => 'App\Resources\Items\UserItem',
            'invitees'  => 'App\Resources\Collections\UserCollection',
            'attendees' => 'App\Resources\Collections\UserCollection',
            'comments'  => 'App\Resources\Collections\CommentCollection',
            'mvp'       => 'App\Resources\Items\UserItem',
        );
    
    }

As you can see we have a list of our attributes that need to be "transformed". The **date**, **from** and **to**  attributes become instances of **Carbon\Carbon**, the relations **author**, **invitees**, **attendees** and **comments** become instances of other resource collection/item classes. This is all handled automatically by simply creating a new instance of the **EventItem** class, and passing in an array. This array can be fetched via Eloquent, so out query inside the event repository would look something like this:

**App\Repositories\DbEventRepository.php**

    public function find($id, $options = null)
    {
        $event = Event::with(['author', 'invitees', 'attendees', 'mvp', 'comments.author'])
                      ->where('id', $id)
                      ->first();
        
        return new EventItem($event->toArray());
    }

The transformations aren't packed with a lot of features right now, but one cool thing you can do is image manipulation. You have to install the **creolab/image** package for that to work and setup your item class something like this:

**App\Resources\Items\UserItem**

    <?php namespace App\Resources\Items;
    
    class UserItem extends \Creolab\Resources\Item {
    
        protected $transform = array(
            'photo' => 'image',
        );
    
    }

The **photo** attribute will then be converted to an image object that has a couple of nice functions. So you can do stuff like this:

    <img src="{{ $user->photo->thumb(100) }}">
    <img src="{{ $user->photo->resize(200, 100) }}">

That's all for now
