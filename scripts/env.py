#!/usr/bin/python
import os, sys, json

SCRIPT_DIR = os.path.abspath( os.path.dirname( sys.argv[0] ) )
BASE_DIR = os.path.dirname( SCRIPT_DIR )
TEMPLATE_PATH = os.sep.join( [SCRIPT_DIR, 'env_templates'] )
OPT_CACHE_PATH = os.sep.join( [SCRIPT_DIR, 'env.cache'])

use_cache = False

def ask_options():
	options = {
		'BASE_PATH' : BASE_DIR,
		'CMS_PATH'  : BASE_DIR + os.sep + 'cms',
		'SHOP_PATH'	: BASE_DIR + os.sep + 'shop',
		'EE_LICENSE': raw_input('EECMS License Number? '),
		'BASE_URL'  : raw_input('Base URL? '),
		'EE_SITE_LABEL': raw_input('EE Site Label? '),
		'DB_EE_HOST': raw_input('EE DB Host? '),
		'DB_EE_USER': raw_input('EE DB User? '),
		'DB_EE_PASS': raw_input('EE DB Password? '),
		'DB_EE_DB'  : raw_input('EE DB Database? '),
		'DB_MAGE_HOST': raw_input('Mage DB Host? '),
		'DB_MAGE_USER': raw_input('Mage DB User? '),
		'DB_MAGE_PASS': raw_input('Mage DB Password? '),
		'DB_MAGE_DB'  : raw_input('Mage DB Database? '),
		'MAGE_CRYPT_KEY':raw_input( 'Mage Crypt Key (Blank to Generate new): '),
	}

	if not options['MAGE_CRYPT_KEY']:
		import hashlib, time
		options['MAGE_CRYPT_KEY'] = hashlib.md5( str(time.time()) ).hexdigest()

	return options

def setup_file(source_path, target_path):
	source = open( source_path, 'r' )
	contents = source.read()
	source.close()
	for key, value in options.items():
		contents = contents.replace('{{-%s-}}' % key, value)
	target = open(target_path, 'w')
	target.write(contents)
	target.close()

if os.path.exists( OPT_CACHE_PATH ):
	if '--cache' in sys.argv or raw_input('Use Cache? [y/n]')=='y':
		use_cache = True
		print "Using Cached Environment Variables"

if use_cache:
	f = open( OPT_CACHE_PATH, 'r')
	options = json.load( f )
	f.close()
else:
	options = ask_options()

for dirpath, dirnames, filenames in os.walk( TEMPLATE_PATH ):
	for filename in filenames:
		source = dirpath + os.sep + filename
		target = BASE_DIR + dirpath.split( TEMPLATE_PATH )[1] + os.sep + filename
		setup_file( source, target )

if '--cache' in sys.argv and not use_cache:
	f = open( OPT_CACHE_PATH,'w')
	json.dump( options, f)
	f.close()
	print "Wrote Environment Cache File"