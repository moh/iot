create database iot_module

/*
    creat table basic_info that store the id,name,type,description 
    and the date of adding the device.      
*/


create table basic_info
(
    m_id int not null AUTO_INCREMENT primary key ,
    m_nom varchar(50) not null,
    m_type varchar(50),
    m_descr text,
    m_date datetime,

    temp_show boolean not null,
    fonc_time_show boolean not null,
    send_data_show boolean not null,
    recv_data_show boolean not null,
    work_cond_show boolean not null,

    temp_data int default 20,
    fonc_time_data int default 0,
    send_data int default 0,
    recv_data int default 0,
    work_cond_data varchar(100) default 'working',
    update_date datetime

)


/*
    le tableau module_log_data est un tableau pour stocker l'historique des evenements,
    ainsi des detailles a propos des message envoye par chaque moduel.
    il y a 6 columns, un id pour chaque column, le id de module, le type de message
    , le contenu de message, le status s'il a vu le message ou pas 
    et  le date de sauvegarder.
*/
create table module_log_data
(
    d_id int not null AUTO_INCREMENT primary key,
    m_id int not null,
    msg_type varchar(50),
    msg_data varchar(300),
    msg_seen_status varchar(20),
    ins_date datetime,

    foreign key (m_id) references basic_info(m_id)
)
