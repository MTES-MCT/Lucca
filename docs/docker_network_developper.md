# Docker network for all developpers

We can start to developp with many containers hosted on the same time on your machine.
In order to let them communicate between themselves, we reorganize communication between all containers.

**Warning** : Make sure each container wan use the same host port to avoid any conflict.

## Create your own network

First step, you need to create you own network. By default, at Numeric Wave, we named this network 'nw-lan-dev' :

````
> docker network create --attachable nw-lan-dev
````

With the option 'attachable', you can ask for your container to subscribe to this network.

## Inspect you network

When you want to check you docker network, you must use this command :
````
> docker network inspect nw-lan-dev
````

As you can see, all containers registered of your network are mentioned, and you can take the local ip for each of them


## Credits

Created by [Numeric Wave](https://numeric-wave.eu)
