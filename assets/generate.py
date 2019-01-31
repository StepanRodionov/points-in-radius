import pymysql
import pymysql.cursors
import settings
import math
from random import shuffle
from scipy.stats import norm as norm

lat = norm.rvs(loc=55.75, scale=0.09, size=settings.set_size, random_state=29)
lon = norm.rvs(loc=37.62, scale=0.107, size=settings.set_size, random_state=72)

many_coords = [(float(lat[x]), float(lon[x]), float(math.cos(math.radians(lat[x]))), float(lat[x]), float(lon[x])) for x in range(0, len(lat))]

conn = pymysql.connect(db=settings.db, host=settings.host, port=settings.port, user=settings.user, passwd=settings.passwd, charset='utf8mb4', cursorclass=pymysql.cursors.DictCursor)
cursor = conn.cursor()

cursor.execute("drop table if exists points");
conn.commit()


create = """
create table points
(
	id int auto_increment,
	lat double not null,
	lon double not null,
	lat_cos double not null,
	g GEOMETRY NOT NULL SRID 4326, SPATIAL INDEX(g),
	constraint points_pk
		primary key (id)
);"""
cursor.execute(create)
conn.commit()

setindex = """
create index simple__index
	on points (lat, lon);
"""
cursor.execute(setindex)
conn.commit()

insert = """insert into points(lat, lon, lat_cos, g) values (%s, %s, %s, ST_GeomFromText('POINT(%s %s)', 4326))"""
cursor.executemany(insert, many_coords)

cursor.close()
conn.commit()