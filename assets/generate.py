import MySQLdb
import settings
import math
from random import shuffle
from scipy.stats import norm as norm

lat = norm.rvs(loc=55.75, scale=0.09, size=settings.set_size, random_state=29)
lon = norm.rvs(loc=37.62, scale=0.107, size=settings.set_size, random_state=72)

many_coords = [(float(lat[x]), float(lon[x]), float(math.cos(math.radians(lat[x])))) for x in range(0, len(lat))]

conn = MySQLdb.connect(db=settings.db, host=settings.host, port=settings.port, user=settings.user, passwd=settings.passwd)
cursor = conn.cursor()

cursor.execute("drop table if exists points");
conn.commit()

geoproc = """
DELIMITER $
DROP FUNCTION IF EXISTS distance_between $
CREATE FUNCTION distance_between (
  tbl_lat double, tbl_lon double,
  lat double, lon double
) RETURNS DECIMAL(6,2) DETERMINISTIC
BEGIN

  SET @dist := 6371000 * 2 * ASIN(
    SQRT(
        POWER(
          SIN((tbl_lat - ABS(lat)) * PI() / 180 / 2),
          2
        ) +
        COS(tbl_lat * PI() / 180) *
        COS(ABS(lat) * PI() / 180) *
        POWER(
          SIN((tbl_lon - lon) * PI() / 180 / 2),
          2
        )
    )
  );
  RETURN @dist;
END $
DELIMITER ;
"""
cursor.execute(geoproc)
conn.commit()


create = """
create table points
(
	id int auto_increment,
	lat double not null,
	lon double not null,
	lat_cos double not null,
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

insert = """insert into points(lat, lon, lat_cos) values (%s, %s, %s)"""
cursor.executemany(insert, many_coords)

cursor.close()
conn.commit()