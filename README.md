# Virtual Groups - Icinga Web 2 module

## Installation

```bash
version=0.3.0-1
wget https://github.com/davbizz/icingaweb2-module-virtualgroups/archive/${version}.tar.gz
tar xfv ${version}.tar.gz
mv icingaweb2-module-virtualgroups-${version} /usr/share/icingaweb2/modules/virtualgroups
```

## Configuration

```bash
mkdir ${ICINGAWEB_CONFIGDIR}/modules/virtualgroups/
cp /usr/share/icingaweb2/modules/virtualgroups/doc/config.example.ini ${ICINGAWEB_CONFIGDIR}/modules/virtualgroups/config.ini
```

Edit:
```bash
vim ${ICINGAWEB_CONFIGDIR}/modules/virtualgroups/config.ini
```