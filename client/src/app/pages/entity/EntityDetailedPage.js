import React, {useEffect, useMemo, useContext} from "react";
import {makeStyles} from '@material-ui/core/styles';
import Paper from '@material-ui/core/Paper';
import Grid from '@material-ui/core/Grid';
import {withAuth} from '@okta/okta-react';
import Box from '@material-ui/core/Box';
import Button from '@material-ui/core/Button';
import FastForwardIcon from '@material-ui/icons/FastForward';
import AttachmentIcon from '@material-ui/icons/Attachment';
import ViewListIcon from '@material-ui/icons/ViewList';
import ContactsIcon from '@material-ui/icons/Contacts';
import EntityListing from '../entity/EntityListing';
import ContactList from '../entity/ContactList';
import {metronic} from "../../../_metronic";
import QuickStatsChart from "../../widgets/QuickStatsChart";
import OrderStatisticsChart from "../../widgets/OrderStatisticsChart";
import OrdersWidget from "../../widgets/OrdersWidget";
import SalesBarChart from "../../widgets/SalesBarChart";
import {useSelector} from "react-redux";
import {
    Portlet,
    PortletBody,
    PortletHeader,
    PortletHeaderToolbar
} from "../../partials/content/Portlet";
import PortletHeaderDropdown from "../../partials/content/CustomDropdowns/PortletHeaderDropdown";
import {Title} from '../home/helpers/titles'
import PermIdentityIcon from '@material-ui/icons/PermIdentity';
import RoomIcon from '@material-ui/icons/Room';
import PersonIcon from '@material-ui/icons/Person';

import PictureAsPdfIcon from '@material-ui/icons/PictureAsPdf';

import BusinessIcon from '@material-ui/icons/Business';
import MailIcon from '@material-ui/icons/Mail';
import Container from "@material-ui/core/Container";
import Link from "@material-ui/core/Link";
import {useHistory} from "react-router-dom";
import {entityDetail} from '../../crud/enitity.crud';
import {OktaUserContext} from '../../context/OktaUserContext';
const useStyles = makeStyles(theme => ({
    root: {
        flexGrow: 1,
    },
    paper: {
        padding: theme.spacing(2),
        textAlign: 'center',
        color: theme.palette.text.secondary,
    },

    adjustment: {
        marginRight: '5px',
    },

    companyinfo: {
        listStyle: 'none',
        padding: '0px',
        minHeight: '100px'

    },
    listItem: {
        marginBottom: '5px'
    }
}));


const EntityDetailedPage = (props) => {
    const {oktaprofile, isAdmin} = useContext(OktaUserContext);
    const classes = useStyles();
    const history = useHistory();
    const [entitydetail, setEntitydetail] = React.useState()
    const [contactList, setContactList] = React.useState([])
    const [attachmentList, setAttachmentList] = React.useState([])
    const [taskList, setTaskList] = React.useState([])

    const [loading, setLoading] = React.useState(true)
    useEffect(()=>{
        fetchDetailedProfile();
    },[])


   const fetchDetailedProfile = async () => {
      const detailedView = await entityDetail(oktaprofile.organization, oktaprofile.email,props.match.params.id);
       setEntitydetail(detailedView.result)
       setContactList(detailedView.result.contacts);
       // setAttachmentList
       setTaskList(detailedView.result.tasks)
       setLoading(false);
   }



    const contactData = {
        columns: [
            {title: 'Name', field: 'full_name'},
            {title: 'Contact Type', field: 'title'},
            {title: 'email', field: 'email'},
            {title: 'Street', field: 'mailing_street'},
            {title: 'City', field: 'mailing_city'},
            {title: 'State', field: 'mailing_state'},
            {title: 'Phone', field: 'phone'},
        ],
        data: contactList,
    };



    const taskData = {
        columns: [
            {title: 'Name', field: 'subject'},
            {title: 'Status', field: 'status'},
        ],
        data: taskList,
    };


    const attachmentData  = {
        columns: [
            {
                title: 'File Name',
                editable: 'never',
                render: rowData => rowData ? (<Link
                    component="button"
                    variant="body2"
                    onClick={() => {
                        history.push(`/dashboard/entity/${rowData.id}`);
                    }}>
                    <PictureAsPdfIcon/> {rowData.file_name}
                </Link>) : 'Sample.pdf'
            },
            {title: 'Attached By', field: 'attachment_by'},
            {title: 'Date Added', field: 'date_added'},
            {title: 'Size', field: 'size', editable: 'never'},
        ],
        data: [
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},
            {id: 1, file_name: 'Profile.pdf', attachment_by: 'Omer Shafqat', date_added: '2020-12-01', size: '2MB'},


        ],
    };



    const {brandColor, dangerColor, successColor, primaryColor} = useSelector(
        state => ({
            brandColor: metronic.builder.selectors.getConfig(
                state,
                "colors.state.brand"
            ),
            dangerColor: metronic.builder.selectors.getConfig(
                state,
                "colors.state.danger"
            ),
            successColor: metronic.builder.selectors.getConfig(
                state,
                "colors.state.success"
            ),
            primaryColor: metronic.builder.selectors.getConfig(
                state,
                "colors.state.primary"
            )
        })
    );

    const chartOptions = useMemo(
        () => ({
            chart1: {
                data: [10, 14, 18, 11, 9, 12, 14, 17, 18, 14],
                color: brandColor,
                border: 3
            },

            chart2: {
                data: [11, 12, 18, 13, 11, 12, 15, 13, 19, 15],
                color: dangerColor,
                border: 3
            },

            chart3: {
                data: [12, 12, 18, 11, 15, 12, 13, 16, 11, 18],
                color: successColor,
                border: 3
            },

            chart4: {
                data: [11, 9, 13, 18, 13, 15, 14, 13, 18, 15],
                color: primaryColor,
                border: 3
            }

        }),
        [brandColor, dangerColor, primaryColor, successColor]
    );

    if(entitydetail){console.log(entitydetail)}

    return (

        <>
            {entitydetail ? <Title title={entitydetail.entity.entity_name}/> : <Title title={''}/> }

            <Grid container spacing={1}>
                <Grid item xs={12} sm={4}>
                    <Portlet fluidHeight={true}>
                        <PortletHeader icon={<PermIdentityIcon className={classes.adjustment}/>} title="Company Info"/>
                        <PortletBody>
                            {entitydetail ?

                                <ul className={classes.companyinfo}>
                                    <li className={classes.listItem}><strong>State ID:</strong> 0</li>
                                    <li className={classes.listItem}><strong>Formation Date:</strong> {entitydetail.entity.formation_date}</li>
                                    <li className={classes.listItem}><strong>Expiration Date: </strong> {entitydetail.entity.expiration_date}</li>
                                    <li className={classes.listItem}><strong>Tax ID:</strong> 09890890</li>
                                </ul> :

                                <ul className={classes.companyinfo}>
                                    <li className={classes.listItem}><strong>State ID:</strong> -</li>
                                    <li className={classes.listItem}><strong>Formation Date:</strong> -</li>
                                    <li className={classes.listItem}><strong>Expiration Date: </strong> -</li>
                                    <li className={classes.listItem}><strong>Tax ID:</strong> -</li>
                                </ul>
                            }
                        </PortletBody>
                    </Portlet>
                </Grid>


                <Grid item xs={12} sm={4}>
                    <Portlet fluidHeight={true}>
                        <PortletHeader icon={<RoomIcon className={classes.adjustment}/>} title="RA Address"/>
                        <PortletBody>
                            {entitydetail ?
                                <ul className={classes.companyinfo}>
                                    <li className={classes.listItem}><PersonIcon className={classes.adjustment}/>
                                        <strong>{entitydetail.AgentAddress.file_as}</strong></li>
                                    <li className={classes.listItem}><RoomIcon className={classes.adjustment}/> {entitydetail.AgentAddress.address} {entitydetail.AgentAddress.address2}
                                    </li>
                                    <li className={classes.listItem}><BusinessIcon className={classes.adjustment}/> {entitydetail.AgentAddress.city}, {entitydetail.AgentAddress.state} {entitydetail.AgentAddress.zip_code}
                                    </li>
                                </ul> :
                                <ul className={classes.companyinfo}>
                                    <li className={classes.listItem}><PersonIcon className={classes.adjustment}/>
                                        <strong> - </strong></li>
                                    <li className={classes.listItem}><RoomIcon className={classes.adjustment}/> -
                                    </li>
                                    <li className={classes.listItem}><BusinessIcon className={classes.adjustment}/> -
                                    </li>
                                </ul>
                            }

                        </PortletBody>
                    </Portlet>
                </Grid>



                <Grid item xs={12} sm={4}>
                    <Portlet fluidHeight={true}>
                        <PortletHeader icon={<FastForwardIcon className={classes.adjustment}/>}
                                       title="Forwarding Address"/>
                        <PortletBody>
                            {entitydetail ?
                                <ul className={classes.companyinfo}>
                                    <li className={classes.listItem}><RoomIcon className={classes.adjustment}/> <strong>{entitydetail.entity.billing_street} , {entitydetail.entity.billing_state} {entitydetail.entity.billing_code}</strong></li>
                                    <li className={classes.listItem}><MailIcon
                                        className={classes.adjustment}/> {entitydetail.entity.notification_email}
                                    </li>
                                </ul> :
                                <ul className={classes.companyinfo}>
                                    <li className={classes.listItem}><RoomIcon className={classes.adjustment}/> <strong>-</strong></li>
                                    <li className={classes.listItem}><MailIcon
                                        className={classes.adjustment}/>-
                                    </li>
                                </ul>

                            }

                        </PortletBody>
                    </Portlet>
                </Grid>
            </Grid>
            <Grid container spacing={5}>

                <Grid item xs={12}>
                    <ContactList loading={loading} tooltip={'Add New Contact'} redirect={true} url={'/dashboard/contact/form/add'} data={taskData} title={'Compliance Tasks'}/>
                </Grid>
            </Grid>

            <Grid container spacing={5}>
                <Grid item xs={12}>
                            <ContactList loading={loading} tooltip={'Add New Contact'} redirect={true} url={'/dashboard/contact/form/add'} data={attachmentData} title={'Attachments'}/>
                </Grid>

            </Grid>
            <Grid container spacing={5}>
                <Grid item xs={12}>
                            <ContactList loading={loading} tooltip={'Add New Contact'} redirect={true} url={'/dashboard/contact/form/add'} data={contactData} title={'Contacts'}/>
                </Grid>
            </Grid>

        </>
    )
}

export default withAuth(EntityDetailedPage);

