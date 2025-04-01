# Deploy on production

## Docker command and start containers

when no configuration has been called, docker source in this order :
* docker-compose.yml
* docker-compose.override.yml


To start containers with default conf and prod configuration who override :
`
docker-compose -f docker-compose.yml -f docker-compose.prod.yml up
`

PS : this command didn't called override configuration

@reference : https://docs.docker.com/compose/extends/

## Credits

Created by [Numeric Wave](https://numeric-wave.eu)
