GREEN="\033[32m"
ENDCOLOR="\033[0m"

echo "${GREEN}---------Init Project---------${ENDCOLOR}"
fin up
echo "*"
echo "*"
echo "${GREEN}---------Composer Install---------${ENDCOLOR}"
fin composer install
echo "*"
echo "*"
echo "${GREEN}---------Delete all contents of shortcut entity---------${ENDCOLOR}"
fin drupal entity:delete shortcut --all
echo "*"
echo "*"
echo "${GREEN}---------Update UUID of site---------${ENDCOLOR}"
fin drush cset system.site uuid "56067190-2c2e-4bf0-964d-d05fa2549384"
echo "*"
echo "*"
echo "${GREEN}---------Import configs---------${ENDCOLOR}"
fin drush cim -y
echo "*"
echo "*"
echo "${GREEN}---------Done!---------${ENDCOLOR}"