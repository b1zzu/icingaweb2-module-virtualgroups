# Virtual Groups - Icinga Web 2 module

## Installation

```bash
wget https://github.com/davbizz/icingaweb2-module-virtualgroups/archive/latest.tar.gz -O icingaweb2-module-virtualgroups-latest.tar.gz
tar -xfv icingaweb2-module-virtualgroups-latest.tar.gz
mv icingaweb2-module-virtualgroups-latest /usr/share/icingaweb2/modules/virtualgroups
```

## Configuration

Setup:
```bash
mkdir ${ICINGAWEB_CONFIGDIR}/modules/virtualgroups/
cp /usr/share/icingaweb2/modules/virtualgroups/doc/config.example.ini ${ICINGAWEB_CONFIGDIR}/modules/virtualgroups/config.ini
```

Edit:
```bash
vim ${ICINGAWEB_CONFIGDIR}/modules/virtualgroups/config.ini
```
